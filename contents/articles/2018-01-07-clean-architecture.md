{
  "title":"Cleaning up your codebase with a clean architecture",
  "description": "A practical example of using a clean architecture to separate your code across layers while using design patterns effectively.",
  "slug" : "clean-architecture",
  "date": "2018-01-07",
  "categories":"Clean code, Layered architecture, PHP, Design"
}

::METAEND::

Let's talk software architecture. Most of us know [MVC](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller), it's the foundation for pretty much every web framework. As your product grows though, problems with MVC will start to appear. Even a relatively simple product can end up with a bloated and messy codebase. MVC is where we start, but what do you do when you need to evolve past it?



Before we go further, let's examine why we have so much trouble explaining the answer.

Here's a common conversation (for developers anyway)

devA: "Our codebase is really messy, how do we clean it up?"
devB: "We need to refactor it, move code into objects, separate the concerns."
devA: "Ok, great. How do we do that?"
devB: "We'll use design patterns and follow the SOLID principles."

This is where we normally stop, we seem to think that's a good enough answer for someone to get started. It's like you asked a carpenter how to make a table, and he just points at his tools and says "use those". The answer is technically correct, but it doesn't tell the whole story and it definitely isn't useful to someone learning to write software (or make tables).* The tools are important, and you need to know what they are, but they're only a small part of the process.
 
## Learning the patterns isn't enough
This is the part that's the most infuriating, learning the patterns isn't enough. Infact, you're probably in a worse place immediately after learning them, because suddenly you have power tools and no clue where to use then.

Learning the patterns opens up a whole new range of questions you have to answer before you can use them effectively.
- Where should I use design patterns?
- How do I decide which one to use?
- Where do I draw my lines of abstraction?
- When should I use interfaces?

Without some guiding principles on how to answer these questions, developers end up creating random boundaries between objects, using patterns wherever they can. This leads to inconsistent code that's worse than it was without all this "design".

[Here is a joke example of this very real problem](https://github.com/EnterpriseQualityCoding/FizzBuzzEnterpriseEdition)

No wonder people complain about "design" and "patterns", we don't do a great job explaining how to use them effectively. 

## Where to start
For me, the moment it all clicked was when I learned about clean architectures**.

A clean architecture is a guide to splitting up your code, giving direction on where certain concepts belong. There are many flavours but they each share the same core concepts. 
- Separate your code by layers
- Put abstractions in the inner layers
- Put implementation details in the outer layers
- Dependencies can only point inward, never outward. 

Here's a fantastic article on the concept that get's it across far better than I ever could.

https://8thlight.com/blog/uncle-bob/2012/08/13/the-clean-architecture.html

Pretty cool right? If you've never seen it before, it's eye opening. 

It gives guidelines on how to separate your concerns and where to draw your boundaries. Once the boundaries are drawn, the patterns become obvious. It's great tool for separating the wheat from the chaff.

Let's look at an example to see what I mean.

## An example

Here we have a simple usecase for setting a user's profile image. This class is used internally by both a controller and a console command, so the code is pretty condensed.


```php
<?php
namespace App\Usecases;

use Ramsey\Uuid\Uuid;
use SplFileInfo;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Domain\User;

class SetProfileImage 
{
	private $filesystem;

	public function __construct()
	{
	    $client = S3Client::factory([
                'credentials' => [
                    'key' => getenv('AWS_ACCESS_KEY'),
                    'secret' => getenv('AWS_SECRET'),
                ],
                'region' => getenv('AWS_REGION'),
                'version' => 'latest',
	    ]);
	    
	    $adapter = new AwsS3Adapter($client, 'bucket-o-images');

	    $this->filesystem = new Filesystem($adapter);
	}

	public function handle(Uuid $user_id, SplFileInfo $image): Uuid
	{
	    $image_id = Uuid::uuid4();

	    $filepath = "profle_image/$image_id.".$file->getExtension()
	    $image_contents = $image->fread($image->getSize());
	    $this->filesystem->write($filepath, $image_contents);

	    $user = User::find($user_id->toString());
	    $user->setProfileImage($image_id);
	    User::where('id', $user_id->toString())->update($user->toArray());

	    return $image_id;
	}
}
```

What's wrong with the above? Well, despite it's condensed nature, it actually has five concepts intermingled, muddying the water.

- Our Application
- Config
- AWS S3
- Flysystem
- Eloquent ORM

You have to understand each of those concepts in order to understand what the class is trying to do internally. That's a surprising numbers of concepts you need to keep in your head in order to understand such a simple class.

To make matters worse, they're not consistent. They each describe their solution in their own language, using details and concepts that do not mix well. For example, ORM language such as `find`, `where` and `update` don't exist in the Flysystem language. Throw application concepts into the mix and you can see why things can get confusing.

It's like trying to read a book where it switches between French, English and Russian at random points, sometimes from word to word. Sure, eventually you could read it, but you'd make a lot of mistakes along the way and you'd be a frustrated mess at the end. So let's try to clean things up.

## Dividing the layers 
First we need to know what we're removing from the application layer, so let's use language as our guide. 

Since we want the language to be application focussed, we need to remove any words unique to the following concepts.
- Config
- Flysystem
- AWS S3
- Eloquent ORM

Instead, we'll replace them with integration points that use the language of the application, rather than the implementation. 
 
This is where design patterns come in. By looking at what you're trying to do and not how you're trying to do it, you can spot common patterns emerging.

Looking at the code, `image` and `user` are core/domain concepts respectively, but some of the code that uses them is pure implementation. These are our integration points, so let's drill down into what we're really doing with "images" and "users".

In our example it's clear that we really want to do two things
- store and retrieve users
- store images

Everything else is an implementation detail. Storing and retrieving things is clearly the repository pattern. So let's create two new application concepts, UserRepository and ImageRepository, and we'll implement them as interfaces.

### Application level

```php
<?php

namespace App\Usecases;

use Ramsey\Uuid\Uuid;
use SplFileInfo;

class SetProfileImage 
{
	private $image_repo;
	private $user_repo;

	public function __construct(ImageRepo $image_repo, UserRepository $user_repo)
	{
	    $this->image_repo = $image_repo;
	    $this->user_repo = $user_repo;
	}

	public function handle(Uuid $user_id, SplFileInfo $image): Uuid
	{
	    $image_id = Uuid::uuid4();

	    $this->image_repo->store($image_id, $image);

	    $user = $this->user_repo->get($user_id);

	    $user->setProfileImage($image_id);
	    
	    $user_repository->store($user);

	    return $image_id;
	}
}

namespace App\Services;

use Ramsey\Uuid\Uuid;
use SplFileInfo;

interface ImageRepository 
{
	public function store(Uuid $image_id, SplFileInfo $image);
}

namespace App\Services;

use Uuid;
use Domain\User;

interface UserRepostiory
{
	public function get(Uuid $user_id): User

	public function store($user);
}

```

That's the application level restructured. We've refined the language and concepts down to the bare minimum that the application needs, expressing our intent. We also created two new concepts that were hidden in the old implementation and we've boiled them down a simple pattern, the repository pattern.

As an aside, we have no problem adding design pattern language to the application layer, because it's a shared language between developers, it aids clarity rather than obscuring it.

Now let's look at the implementations.

### Infrastructure

```php
<?php
namespace Infrastructure\App\Services;

use App\Services\ImageRepository;
use Ramsey\Uuid\Uuid;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

class S3ImageRepository implements ImageRepository
{
	private $filesystem;

	public function __construct()
	{
	    $client = S3Client::factory([
                'credentials' => [
                    'key' => getenv('AWS_ACCESS_KEY'),
                    'secret' => getenv('AWS_SECRET'),
                ],
                'region' => getenv('AWS_REGION'),
                'version' => 'latest',
	    ]);

	    $adapter = new AwsS3Adapter($client, 'bucket-o-images');
	    
	    $this->filesystem = new Filesystem($adapter);
	}

	public function store(Uuid $image_id, SplFileInfo $image)
	{
	    $filepath = "profle_image/$image_id.".$file->getExtension()
	    $image_contents = $image->fread($image->getSize());
	    $this->filesystem->write($filepath, $image_contents);
	}
}

namespace Infrastructure\App\Services;

use App\Services\UserRepostiory;
use Ramsey\Uuid\Uuid;
use Domain\User;

class EloquentUserRepostiory implements UserRepostiory
{
	public function get(Uuid $user_id):User 
	{
		return User::find($user_id->toString());
	}

	public function store($user)
	{
		User::where('id', $user_id->toString())->update($user->toArray());
	}
}
```
And that's that, we've separated our usecase's code across two layers. 

## Why is this better?
### Easier to read
From a language perspective, this is much cleaner. It's focussed on the language of what our application wants to do, not how it intends to do it. This cohesive language lowers the barrier to comprehension. In short, there are fewer concepts to parse in order to understand it.

### Testing
It's far easier to test. The original has so much going on that testing it is difficult. Our acceptance tests would have to connect to S3 and the DB to prove that the code worked. This would couple our tests to our implementations, making the code brittle and expensive to change. 

In the new version, each layer can be tested in isolation, with unit tests for our usecases and integration tests for our implementations. This makes our acceptance tests much easier to verify, we only need to check the output, rather than the side effects.

### We're using patterns effectively
In the above, we used the repository pattern in a way that made sense to the application, rather than using whatever pattern we liked at the time. This keeps things focussed and stops us going design pattern mad. After failing with design patterns for so long, it feels really nice to use them effectively.

### Loose coupling
The above is actually a [SOLID](https://en.wikipedia.org/wiki/SOLID_(object-oriented_design))*** example of the [dependency inversion principle](https://en.wikipedia.org/wiki/Dependency_inversion_principle) in action. We've inverted the dependencies of our code, ie. the application is no longer dependant on the database, instead it is the database that depends on the application. Simply put, this means that a change in the database will no longer affects the business logic, so there's less risk in changing it.

### Changing implementation
Another benefit, switching implementations is incredibly easy. Imagine we needed to switch storage mechanism, say from MySQL to MongoDB. Instead of gutting our usecase, we just create a new implementation of that interface with the MongoDB lib under the hood. Boom, we're done. No changes required at the application level.

This example highlights how useful language can be as a guiding hand when writing layered code. The newly introduced language of mongodb did not bleed into the language of the application layer, it stayed the same, no change was necessary. This is a sure sign that we've separated our layers correctly.

## Why is this worse?
Let's be honest, it's not all sunshine and roses, so let's address the `const ELEPHANT = true;` in the `class Room`;
For a start the codebase is larger, there are now more files and there is more code to read. It also takes longer to write. You have to spend time extracting the code into layers, taking the time to think about the design of your code, so there is an element of sacrifice there. This could get in the way if it's throwaway software that you just want to get out the door and never see again****.

I don't think the above are reasons enough not to do it though. Software changes, especially if it's core to a product, so it's worth the extra time now to save a lot of time later.

Plus, you get better at it the more you do it. Eventually you'll notice the same patterns again and again, and you'll be able to create the abstractions at the beginning, intuitively separating what you want to do from the tools you're using to do it. 


## Can we go further
Looking at the above, we're using a Uuid lib pretty much everywhere. We could abstract it of the code, using an interface instead, so we'd have an ID interface that uses the Uuid lib internally.

The reason we haven't is simple, it's not worth the effort. The pay-off in clarity would be too small. We use Uuids at pretty much every layer of the codebase, and we've found the overhead of interpreting the extra language of the Uuid lib to be pretty small. So we've simply made it a part of our core***** layer, instead of considering it an implementation details. This means we can use it any other layer directly. 

## When to do it
The above raises an important point, this approach to separation of concerns is a technique to use when needed, sometime you don't need it. Above we made a judgement call on what to extract into it's own layer and what to make part of the core. There are other guidelines, but really it boils down to clarity. 

If you find things getting confusing in a layer, consider moving code/concept into another layer, or creating the layer if needs be.

## Writing clean code
Writing clean code is hard, and using patterns haphazardly just makes things worse. That's why I recommend applying the clean architecture mindset, it's a simple technique for cleaning up your code. It helped me, and I hope it can help you.

*I think this is because we put all our focus on the tools, and not on the process. Everyone is so busy arguing over which chisel is best, they start thinking that the tools are all there is to designing software.

**Also know as Hexagonal Architecture or Onion Architecture. I was introduced to the concept by Vaughan Vernon in his book, [Implement Domain Driven Design](https://www.amazon.com/Implementing-Domain-Driven-Design-Vaughn-Vernon/dp/0321834577) and I have never looked back

***Worst pun ever

****The only software I've never revisited is the kind that failed to produce value. If it gets used, it will change.

*****Quick suggestion, "Core" should only include code that's standalone, it shouldn't include any code that connects to outside services or has side effects, like database code.