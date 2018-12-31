---
title: Immediate Consistency in Event Sourcing
published: true
description: Strategies for immediate consistency in event sourcing 
cover_image: https://thepracticaldev.s3.amazonaws.com/i/ad1vtbq52gf9kpwdkplz.jpg
tags: event-sourcing, ddd, php
---
A continuation on the [Messy Event Flows series](https://dev.to/barryosull/messy-event-flows-part-1) (if you could call it that). You don't need to read it first, but if you're interested, give it a shot, it gives a little more context. 

In the [last article](https://dev.to/barryosull/messy-event-flows-part-2---what-it-should-be) we talked about our domain wide (cross aggregate) constraints, and mentioned one way to implement them in an [event sourced system](https://dev.to/barryosull/event-sourcing-what-it-is-and-why-its-awesome), but we haven't gone into any real detail since then. 

So let's look at one of the constraints and figure out how to implement it.

# The constraint

>When a book is created, it has to have a unique number within a category. 

How do we ensure that the above constraint is met? Seems like a simple question, but let's really dig into it. 

# The Usecase
This is what our usecase looks like for creating a book, it's used by our HTTP Controller.

```php
<?php
namespace App\Usecase;

use Domain\Projections\BookNumberGenerator;
use Domain\Aggregate\Book;

class AddBookToCategory
{
	private $book_repo;
	private $book_number_generator;

	public function __construct(Book\Repo $book_repo, BookNumberGenerator $book_number_generator)
	{
		$this->book_repo = $book_repo;
		$this->book_number_generator = $book_number_generator;
	}

	public function run($category_id, $book_id, $title, $author)
	{
		$number = $this->book_number_generator->generateNextNumberInCategory($category_id);

		$book = new Book($book_id);
		$book->create($category_id, $title, $author, $number);
		$this->book_repo->store($book);

		$this->book_number_generator->play($book->changes());
	}
}
?>
```

What's the above doing? 
- Generates the book number via the projection
- Creates the book with that number
- Stores the book
- Plays the events into the BookNumberGenerator projection, ensuring it's up-to-date. 

In order for the constraint to be met, we need our projection of book numbers to be immediately consistent. That's why we play the events into the projection immediately, rather than allowing a background process to take care of it.

## Immediate consistency
Immediate consistency means that as soon as something happens, everyone is immediately informed, there is no delay. Ie. the entire system (service) has a consistent view on the data. This means that the projection that assigns book numbers has to be updated as soon as a book is created, otherwise it could potentially assign numbers that have already been assigned. Immediate consistency is a blocking operation, it forces your app to lock data and it can cause issues, particularly when you have multiple processes operating on the same dataset.

## Domain Contraints
Let's not jump the gun and jump straight to implementation, let's look at it from a domain perspective, what do we want to happen? Well, if someone tries to create a book with a duplicate number, it should fail. This is something the person performing the operation cares about, so we want it to fail explicitly. Since that's the case, we'll model the failure as a domain exception, let's say "BookNumberNotUnique", and make sure that it's thrown whenever this problem occurs.

## Implementation 
We decided to make this simple. When the events are played into the projection "BookNumberGenerator", if there's a duplicate, it should fail and throw the domain exception "BookNumberNotUnique". We're using MySQL for our projection implementation, so we'll implement this rule in infrastructure by adding a unique index across book numbers and category IDs. When the index fails, a MySQL exception will occur, and we'll translate that exception into our domain exception and throw that. Pretty simple.

As we're building a [clean architecture](https://dev.to/barryosull/cleaning-up-your-codebase-with-a-clean-architecture), our projections are usually dumb interfaces with a concrete implementation in the corresponding technology. At this point, we don't care if the implementation is MySQL or MongoDB or even a file system, all we care about is that the constraint is met.

## Testing the constraint
To make sure the constaint is met, we need to write a test for it. You could do this using acceptance tests, but I'd rather do this as an integration test, more control, less noise. To start, we'll write a testcase that ensures the contract for the BookNumbersProjection is met, nothing more.

```php
<?php
namespace Test\Integration\Domain\Projections;

use Domain\Events\BookAdded;
use Domain\Exceptions\BookNumberNotUnique;
use Domain\Projections\BookNumberGenerator;
use Domain\ValueObjects\CategoryId;
use Domain\ValueObjects\BookNumber;
use Domain\ValueObjects\BookId;

abstract class BookNumberGeneratorTest extends \PHPUnit_Framework_Testcase
{
	protected $book_number_generator;

	public function setUp()
	{
		$this->book_number_generator = $this->makeBookNumberGenerator();
	}

	protected abstract function makeBookNumberGenerator(): BookNumberGenerator;

	// Factory method to make tests easier to build
	private function makeBookedAddedEvent(CategoryId $category_id, BookNumber $book_number): BookAdded
	{
		$book_id = BookId::generate();
		return new BookAdded($book_id, $category_id, $book_number)
	}

	// Existing test
	public function test_gives_next_valid_book_number_for_category()
	{
		$category_id = CategoryId::generate();
		$book_number = new BookNumber(1);
		$event = $this->makeBookedAddedEvent($category_id, $book_number);
		$this->book_numbers->play([$event]);

		$expected_book_number = new BookNumber(2);
                $actual_book_number = $this->book_numbers->generateNextNumberInCategory($category_id);

		$this->assertEquals($expected_book_number, $actual_book_number);
	}

	/** 
	 * New tests 
	 **/
	public function test_cannot_have_duplicate_books_numbers_in_a_category()
	{
		$category_id = CategoryId::generate();
		$book_number = new BookNumber(1);
		$event_a = $this->makeBookedAddedEvent($category_id, $book_number);
		$event_b = $this->makeBookedAddedEvent($category_id, $book_number);

		$this->expectException(BookNumberNotUnique::class);

		$this->book_numbers->play([$event_a, $event_b]);
	}

	public function test_can_have_duplicate_book_numbers_across_categories()
	{
		$category_id_1 = CategoryId::generate();
		$category_id_2 = CategoryId::generate();
		$book_number = new BookNumber(1);

		$event_category_a = $this->makeBookedAddedEvent($category_id_1, $book_number);
		$event_category_b = $this->makeBookedAddedEvent($category_id_2, $book_number);
		
		$this->book_numbers->play([$event_category_a, event_category_b]);
	}
}
?>
```
There we go, that's the base testcase. You'll notice this is an abstract test class that doesn't reference MySQL, that's because we want to keep implementation details out of the contract test. Here is the integration test for the MySQL implementation.

```php
<?php
namespace Test\Integration\Domain\Projections\BookNumberGenerator;

use Test\Integration\Domain\Projections;
use Infrastructure\Domain\Projections\MySQLBookNumberGenerator;

class MySQLTest extends Projections\BookNumberGenerator
{
	protected abstract function makeBookNumberGenerator(): Projections\BookNumberGenerator
	{
		return new MySQLBookNumberGenerator();
	}
}
?>
```

And that's the test. We'll not dig into the "MySQLBookNumberGenerator" class itself, you can imagine how it works internally, but it good to note that it boots up it's own SQL client internally, so we don't need to muck around with dependencies. It's good enough for now.

## What happens if the constraint fails?
If you're looking closely, you'll see a potential flaw. By the time we play the events into the projection, we've already stored the book in the repo, won't this cause things to break? Thankfully, no. We have a usecase runner class that responsible for running our usecases, like the one above. It wraps each run of a usecase in a DB transaction. This ensure that the operation is all or nothing.

# Problems with this implementation
The above has one glaring problem. If two or more people try to create a book in a category at the *exact* same time (~50ms of each other), only one of the requests will complete, the rest will fail. There's really no way around this. It's incredibly unlikely, but it should still be monitored, so whenever we see this particular exception we send it to our error tracker.

So what if this becomes a major problem, ie. constant failures? It's unlikely, but it'd be nice to have a plan in place if it does become an issue for our customers. 

We have three options here. The first two kick the can down the road, the third solves the problem permanently, but introduces a new domain concept.

## 1. Retry the usecase if it fails
The simplest fix. If the usecase fails due to the domain constraint above, we simply retry the usecase again. A simple try/catch takes are of this. This is not a permanent solution, but it will work for quite a while.

## 2. Queue requests instead of blocking them
Rather than force one of the processes to fail, we queue them, forcing our usecase to wait for others to finish before it runs. This can be done with any number of technologies (Redis/RabbitMQ/MySQL/etc...) and it will allow us to handle this problem without failures. It's not perfect though, as it too will fail eventually. At some point we'll get too many requests in the queue and the connections will timeout/fail.

As I said, the first two are stop gap solutions. If we want a more permanent solution, we need to change our assumptions of the constraint.

## 3. Soften the constraint and use a process manager
Let's look at the constraint again.

>When a book is created, it has to have a unique number within a category. 

Anything we can do about this? Well, we have to adhere to `it has to have a unique number within a category`, that's just a hard constraint, otherwise the book could not be referenced. 

What about the first part though, `When a book is created`?

This is where things get interesting. It turns out that we modelled the constraint naively and made it overly aggressive. The book number doesn't have to generated immediately, it could happen a second after the book was created and that would be fine. The number is only used when referencing the book, and that won't occur until the book is actually in circulation, which happens well after it's added to the system.

BTW, the above is an important insight. In software development, we tend to model our constraints naively, making them overly aggressive, creating "hard constraints" out of thin air. Most system wide constraints are actually soft, or not immediately consistent. Making them hard seems like a good idea, but it makes our systems brittle.

## The ideal implementation
With this new insight, how would we handle this constraint? It turns out that the numbers can be generated eventually, not immediately, so we can use a "Process Manager" to take care of this. It listens for "BookCreated" events, every-time it receives one it assigns that book a number. It uses a projection internally to keep track of assigned numbers per category, so it always knows the right number to assign.

This is what this looks like with a process manager in the event flow.

![Book Number generation process](https://thepracticaldev.s3.amazonaws.com/i/8dwoy5jil4432yy8zupr.png)

This process manager will process events as quickly as it can, assigning numbers every-time a book is created. If multiple people try to create a book at the same time, it will be fine, since a single process is taking care of the problem the numbers are guaranteed to the unique, no race conditions. The above model can handle a ridiculous numbers of books being created simultaneously, so we've effectively removed the bottleneck created by this constraint.

# Is this overkill?
We went into a lot of detail for a such a simple constraint, and most people would consider the above a little over the top. It can make event sourcing and DDD seem daunting, but I'd say this, you probably have these problems in your CRUD app, you just aren't aware of them. 

If you have a hard constraint in your app, such as a unique number or email address, you will face exactly the problems we've discussed above. The only difference is that you'll view your implementation as the constraint, rather than an expression of it, and things will get messy/complicated very quickly. Honestly, you'll most likely be unaware of these problems until they bite you in the ass, I know I was.

This is the advantage of EventSourcing and DDD, it forces you to think about your constraints, rather than making naive assumptions that lead to broken implementations.

# Conclusion
This article covered a few topics, but at it's core it's about immediate consistency in event sourcing. From the above, you can see that's it's entirely possible and actually fairly simple to do. 

Personally I think it's fine to model domain wide constraints as immediately consistent at the start of a project. It will take a couple of years before you'll run into the problems above, and when you do, there are simple solutions. We only solve problems when we think they're going to become problems, so as long as you have a monitoring/warning system, you'll be fine.

Thanks for reading, I hope you found it useful. If you have any suggestions or thoughts, please let me know in the comments, always happy to discuss.
