---
title: "Projection Building Blocks: What you'll need to build projections "
published: true
description: The building blocks of a robust projection system
tags: event sourcing
cover_image: http://barryosull.com/images/ff21e4a8-0140-4d27-9172-46e19b3dcd33.jpg
---

Let’s talk about projections. This topic is quite large, so this is the first part in a four part series on projections.

1. **Projection Building Blocks**: What you'll need to build projections
1. **Broadcasting events in PHP**: Techniques and technologies   
2. **Designing Projections**: How to design and implement real world projections
3. **Projection DevOps**: Continuously deploying new/updated projections with zero downtime

If you've read my previous articles, you should have the basics of [event sourced](event-sourcing-what-it-is-and-why-its-awesome)/[event driven](https://dev.to/barryosull/event-granularity-modelling-events-in-event-driven-applications-e50) Command Query Responsibility Segregation ([CQRS](https://martinfowler.com/bliki/CQRS.html)) systems. At its core there are two concepts, a command side that outputs events and a query side that reads them.

Up until now I've focussed primarily on the command side, i.e. how we model state changes in our apps. I mention projections, but always with a hand-wavy statement and without any real detail. Let's do something about that.

![Projections](https://res.cloudinary.com/practicaldev/image/fetch/s--O4HIIlNN--/c_limit,f_auto,fl_progressive,q_auto,w_880/https://thepracticaldev.s3.amazonaws.com/i/lhhlvel6ifqaj0y4otb7.png)


# What are projections?
Projections are a necessary part of any event sourced or CQRS system. These systems don't rely on a single generic data source such as a normalised MySQL database. Instead you build up your data sets by playing through the events, i.e the “film”, "projecting" them into the shape you want. This allows lot of flexibility as you're no longer bound by a single data model on which you have to run increasingly monstrous SQL queries (12+ joins anyone?). With projections you can build a data model specifically for the problem/question at hand.

For instance, say your app has the following requirements:
1. Webapp needs to fetch a user, their active cart, and the cart's items, all as a single document.
2. Marketing needs a list of how much each user spends over a 6 month period, broken down month by month.

Building a generic data-model that can produce both answers is possible, but it's difficult, and leads to complex SQL statements and brittle data structures. Instead, it's much easier to build up a custom dataset for each use-case, keeping them independent and minimal. 

In the above, the webapp would listen for the appropriate events and build a tree of the user, their cart and its items. This structure would get stored in a document DB, such as MongoDB, and fetched later when it’s needed. Nice and easy.

For the Marketing report, it’s a little different. We listen for the same events, but we don’t build the same structure. Instead we keep track of three pieces of data: the users_id, the month, and how much they spent that month. We store that data in a RDBMS, such as MySQL, so that it’s easy to query.

These two datasets are simple yet completely different. They are each designed to answer their specific question and are thus simpler to understand and build. That's the power of projections.

![Projections](https://thepracticaldev.s3.amazonaws.com/i/7pkqurqyjf7rpgksf1as.png)

# The Building Blocks
Building a robust* projection system is not a trivial task, as there are many concepts and moving parts. Before you can build one, you need to understand each piece in isolation, then see how they all work together.

*An apt word that has been ruined for me due to overuse in college

![Projection Building Blocks](https://thepracticaldev.s3.amazonaws.com/i/rsdnvlottw5irdvm0be9.png)

## Event
An event is a named object that represents some discreet change that occurred in your system. It's usually modelled as a class with a collection of properties, giving just enough formation to be useful.

Eg.

```php
<?php namespace Domain\Selling\Events;

use ...;

class CartCreated extends Event
{
    /** @var Uuid */
    public $cart_id;

    /** @var Uuid */
    public $customer_id;     
}
```

Events also contain some generic meta information, info that each event should contain to make easier to work with. I'd recommend the following.
- the ID of the event (unique)
- when the event happened
- the actor that triggered the event (could be a person or a system process)
- the version of the event (events can change shape over time, we'll get into this later)


```php
<?php namespace Domain\Events;

use ...;

abstract class Event 
{
    /** @var Uuid */
    public $id;

    /** @var Carbon */
    public $occurred_at;

    /** @var Actor */
    public $actor;

    /** @var integer */
    public $version = 1;
}
```

## EventStore and EventStream
The EventStore is your access point to all the events that have ever occurred in your system. You give it a position/point in time and it gives you an EventStream that you can iterate through. From there it’s a simple as iterating through the stream until there are no events left.

```php
<?php
$last_position = "b70931a6-b330-4866-97b4-0710c8ad5d3e";
$event_store = new EventStore();
$event_stream = $event_store->getStream($last_position);
while ($event = $event_stream->next()) {
	// Do things
}
?>
```
Under the hood Event stores and streams can be implemented in any number of technologies, SQL, NoSQL, Kafka, even the FileSystem. As long as they meet the following constraints you're good.

1. You can start reading from any point in the stream
2. The events are read in the order that they occurred
3. The event stream is append only (history doesn't change)

Now that we have a way to read events from the log, let's look at what we do with them.

## Projector
In order to build up a data set, you need to listen for a set of events. That's where projectors come in. Their job is to listen for events then pass them through to the projection that's building up the dataset.

There are many ways to do this, but this is my preferred one.

```php

<?php namespace App\Projections\Carts;

use Domain\Shopping\Events;

class Projector
{
    private $projection;

    public function __construct(Projection $projection)
    {
        $this->projection = $projection;
    }

    public function whenCartCreated(Events\CardCreated $event)
    {
        $this->projection->createCart($event->cart_id, $event->customer_id);
    }

    public function whenItemAddedToCart(Events\ItemAddedToCart $event)
    {
        $this->projection->addItemToCart($event->cart_id, $event->item);
    }

    public function whenItemRemovedFromCart(Events\ItemRemovedFromCart $event)
    {
        $this->projection->removeItemFromCart($event->cart_id, $event->item_id;
    }

    public function whenCartCheckedOut(Events\CartCheckedOut $event)
    {
        $this->projection->checkoutCart($event->cart_id);
    }
}

```
As you can see above, the method signature defines the event we're listening for, and the method itself extracts the data and feeds it through to the projections.

## Projection
A projection is the result of "projecting" a sequence of events. It has two categories of functions: commands and queries ([standard CQS pattern](https://martinfowler.com/bliki/CommandQuerySeparation.html)). Commands change the shape of the underlying dataset. Queries fetch results from the dataset, usually to answer business questions or to present data.

Here's a simple example that looks after a customer's cart and it's items.


```php
<?php namespace DoctrinePlayground\App\Projections\Carts;

use Doctrine\DBAL\Connection;
use Ramsey\Uuid\UuidInterface;
use Carbon\Carbon;
use DoctrinePlayground\Domain\Selling\Entities\Item;

class Projection
{
    private $db;

    const CARTS_TABLE = 'carts';
    const CART_ITEMS_TABLE = 'cart_items';

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /** Example Commands **/

    public function createCart(UuidInterface $cart_id, UuidInterface $customer_id, Carbon $created_at)
    {
        $this->db->insert(self::CARTS_TABLE, [
            'cart_id'     => $cart_id,
            'customer_id' => $customer_id,
            'active'      => true,
            'created_at'  => $created_at->format("Y-m-d H:i:s"),
        ]);
    }

    public function addItemToCart(UuidInterface $cart_id, Item $item)
    {
        $this->db->insert(self::CART_ITEMS_TABLE, [
            'cart_id'     => $cart_id,
            'item_id'     => $item->item_id,
            'item_ref'    => $item->reference
        ]);
    }


    /** Example Queries **/
    
    public function getActiveCart(UuidInterface $customer_id)
    {
        $cart_arr = $this->db->fetchAssoc('SELECT * FROM '.self::CARTS_TABLE.' WHERE customer_id = ? AND active = 1', [$customer_id]);

        if (!$cart_arr) {
            return null;
        }

        $cart = (object)$cart_arr;

        $items = $this->db->fetchAll('SELECT * FROM '.self::CART_ITEMS_TABLE.' WHERE cart_id = ?', [$cart->cart_id]);

        $cart->items = array_map(function($item) {
            return (object)$item;
        }, $items);

        return $cart;
    }
}

```

### An aside on implementation
If you plan to have many different implementations of the same projection, I'd recommend extracting the methods into an interface, otherwise don't bother. If you do write an interface, some devs advocate [separating the Command and Query sides](https://www.erikheemskerk.nl/event-sourcing-cqrs-querying-read-models) into their own interfaces, but I think this is overkill; We did this for each of our projections and it just made the code harder to navigate, understand and change, ie. it didn't bring any real benefit.

## Projectionist
In keeping with the projection metaphor we also have a projectionist. The projectionist is responsible for playing a collection of projectors. Internally, the projectionist does this by keeping track of where each projector is in the stream (by event position or event id), then plays each projector forward from that point, recording the last event that each projector has seen.

## Projector Position Ledger
As mentioned above, the projectionist needs some way to keep track of where each projector is in the event log. That's where the Projector Position Ledger comes in. This is a simple data store that keeps track of each projector and it's position. Its fairly simple and can be implemented in any storage technology.

A handy idea, it should also keep track of whether a projector is broken or not. If a projector tries to run and an unexpected exception is thrown, the projector should be marked as "broken" and the projectionist should stop attempting to play it. This way you won't try to keep playing a broken projector, filling up your bug tracker with duplicate exceptions.

## Event Upgrader
This component is a little more advanced, but it's still worth mentioning. Sometimes you'll need to change the shape of events, usually by adding or changing properties. When this happens, you'll need to "upgrade" the event shape. This is why each event has a "version" attribute, so we can check the version of the event and apply the appropriate upgrader if it's required.

This component lives in the event stream and manipulates the event data before it is deserialized into the actual event classes. It is used by both the command and query side.

I won't get into too much detail here, [as this is quite complex](http://danielwhittaker.me/2015/02/02/upgrade-cqrs-events-without-busting/), just be aware that it exists. Think of event upgraders as migrations for events that are run on the fly and you're most of the way there.

# Putting it all together
Those are all the components, so let's look at how they all work together.

![Projectionist](https://thepracticaldev.s3.amazonaws.com/i/ea3uvjpnhca5wokt6tnx.png)

The projectionist is given a collection of projectors. They go through each projector, and fetch an event stream that starts after the last event each has seen. The new events are played through the projector, and on completion the projectionists records the last even seen by the projector.

And that's that, those are the pieces you need to build an effective projection system, at least at the start.

# Next Steps
There's one thing missing from above, how do you trigger your projectionist to play a projector? This is a complex question, especially if you're using PHP. So that's why I'm dedicating the next article to just that. We'll go through some implementation details, exploring the pros and cons of each, then settle on what I think is the best solution.

The third article will dive into building complex projections using various technologies designed to solve specific problems. In it we’ll highlight some of the techniques our team has found useful when building, designing and implementing projections.

The fourth article will dig into projection versioning, seamless releases and some more advanced concepts related to the projector/projectionist side of things.

Until then, best of luck!

