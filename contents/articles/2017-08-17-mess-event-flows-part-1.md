---
title: "Messy Event Flows: Part 1"
published: true
description: Restructuring events flows in event sourced apps
tags: event-sourcing, event-storming, ddd
cover_image: https://thepracticaldev.s3.amazonaws.com/i/x9hxvpnu64nynaw1bj3j.png
---

# Intro
I've been trying to write lately and I've been finding it difficult. Then I watched a video ["Don't create, document"](https://www.youtube.com/watch?v=RVKofRN1dyI). TL;DR: instead of trying to write a how-to, focussing on a solution, why not document what you're doing, as you work towards a solution? I really like this idea, as it's a problem first, solution second approach, which is how any process of discovery or creation should begin (true of coding and any other endeavour really).

So without further ado, let's get into a problem we've been facing.

# The current state of our system
We have an [event sourced](https://martinfowler.com/eaaDev/EventSourcing.html) system, this means it's entirely event driven and all state is derived from events. We currently have three core objects/concepts (names changed to avoid exposing our business internals, appropriate metaphors used instead, should be grand).

- Category
- Book
- Edition

They're tiered in a many to one relationship.

The above concepts are our aggregates, as each object has an independant lifecycle.

## Our Constraints
1. Categories have one or more Books
2. Books have one or more Editions.
1. When a Writer creates a Category, a Book must be created for that Category, and a Book Edition must be created for that Book, otherwise the user has nothing to work with.
2. Across a department, Categories must have a unique number that represents them. This number increments and there can be no duplicates.
3. Inside a Category, each Book must have a unique number. This number increments for each Book in the Category and it must be unique within the Category.
4. A Book can have many BookEditions, each Book Edition must have a unique number that represents them within the parent Book. This number increments for each Edition of the Book and it must be unique for the Book.
5. These three values combined are used to reference a specific Book Edition, ie. they are it's reference number.

## Our Model
This is how we've currently modelled our events, aggregates and constraints. This is called a temporal model, it shows how things change over time. The process of discovering this is often called [Event Storming](https://techbeacon.com/introduction-event-storming-easy-way-achieve-domain-driven-design). 

### Legend
- User issued Commands are blue 
- Constraints are red
- Events are yellow
- Dotted circles are aggregates
- Dotted arrows link events to the constraints that require them

![Event Flow/Temporal model](https://thepracticaldev.s3.amazonaws.com/i/i038hdo2g0e6xxhigko0.png "Event Flow/Temporal model")
Click to zoom.

## What's the problem?

I'm not happy with two of the constraints that generate numbers. They're currently inside the aggregates, but they're listening to events across the domain, not just the ones produced by that aggregate instance. This feels like it should be elsewhere, or at least modelled differently. 

Data is distributed poorly across some events. The "Book Edition Added" event is created in the Book aggregate, yet it contains the Edition Number, shouldn't that information be controlled by the Edition aggregate? This makes both the constraints and events awkward to use and difficult to reason about.

The train of object creation is also a bit of a pain, Category=>Book=>Edition. These are meant to be aggregates, so creating all three all at once is not inline with the aggregate pattern. However, there's a valid reason it's like this. When creating a category, the Edition events require data from the command, and it doesn't make sense to piggy back that data on higher level events, just so Edition has access to the data. Creating them all at once makes this simpler. I'm unsure about this one TBH.

There are also two competing patterns for how we keep track of incrementing numbers. Category numbers and Book numbers are produced by constraints that listen to domain wide events, but the Edition numbers are produced by a constraint that only listens to events from a single Book aggregate. These are two different ways of solving the same problem, which implies we haven't nailed down the pattern for this. This makes things confusing and it's partially responsible for the data being in the wrong events.  

There are redundant events. Look at "Book Edition Added", why does this exist, if there's always a "Edition Started" event afterwards? Well, it exists so we can produce the Category number without listening to events across the domain, not a great reason to add a redundant event in hindsight.

Our domain wide constraints aren't a great solution either. We need to ensure that numbers are generated sequentially across processes and the data must not be corrupted. We're using a transaction to take care of this behind the scenes, but that transaction blocks others processes that are also trying to generate these numbers. This could become a bottleneck in the near future.

## It's hard to change
All of the above is problematic, as we recently had modify our flows and we found it hard to reason about the changes we needed to make. As a result, it took longer than anticipated, which frustrated pretty much everyone. We can do better.

# What are our options?

I have a couple of ideas on how to solve these problems, but before we explore any of them, we have to ask "What's our core problem?".
Our problem boils down to a lack of understanding of the domain, we've modelled our events and constraints in an awkward manner that suits the patterns we use to implement the system, not the domain itself. We don't know if that's how the model should actually look/work. We need clarity.

That's why we need a better understanding of our domain and it's constraints. Spending time on anything else is probably a pre-optimisation, we don't know what the end result should look like, so we'd be jumping the gun with any proposed solution.

So let's produce a fresh model of the domain, an up-to-date flow that removes noise and gives us direction. After all, it's the core of what we're trying to do.

The best way to do this is an event storming session. We'll model the system as we understand it, rather than how we've currently modelled it (We already have that). We should also take into consideration all the constraints and patterns we've created/discovered since the last session. 

Once we have this, we'll have a better idea of where things belong, and how the flow actually works. This will guide us in the design of our events, their data, and the constraints that listen to them.

# Conclusion
This seems like a good place to stop. I've defined the problem we have, and realised we need to understand our domain better if we want to make the code better. Once we have this, the next steps will be a lot clearer. 

Till next time people.

[Messy Event Flows: Part 2 - What it should be](https://dev.to/barryosull/messy-event-flows-part-2---what-it-should-be)