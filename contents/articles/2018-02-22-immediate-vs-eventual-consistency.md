---
title: "Immediate vs eventual consistency"
published: false
description: "When it comes to building and playing projectors, how quickly should you process events and what are the trade-offs"
tags: event sourcing
---
In the last article we looked at the building blocks of projectors. So assuming we have events, projectors, and a projectionist, how do we trigger the projectionist to play events into the projectors? Well not so fast! Before we can dig into the technologies, we need to understand how we want this system to work. The biggest consideration being, are events processed immediately or eventually?

# Immediate vs Eventual Consistency
When it comes to projections there are two options, immediate or eventual consistency. With immediate, events are processed by projectors as soon as they happen. With eventual, events get processed in a different transaction by a different process at a later time (usually a split second later). Immediate is an all or nothing operation, if anything goes wrong then the entire process is halted, no events are stored and no events are processed. Eventual is a staggered operation, events can get stored successfully, but each projector can fail independently of each other at a later time.

## Reasons to not use Immediate Consistency
From the above it may seem like immediate is the obvious answer, it looks simpler and has less moving parts. Well, that simplicity is a lie. It turns out immediate is far more complex and opens up a can of worms; here are some questions it forces you to answer.

### 1. What happens if one of the projectors is slow?
Say one of the projectors performs an expensive process, like connecting to a slow external service (eg. sending an email). With immediate consistency we have to wait for it to complete before we can let the user continue. Worse, if we're using transactions to ensure data integrity across a domain (e.g. email address is unique), then you're potentially slowing down other processes, not just this one. Talk about a bottleneck.

### 2. What happens if one of the projectors has an error?
If a projector has an error, what do you do? The ideal solution is to roll back all changes, act like it never happened. This is easy enough if you're using a single DB to store everything (transactions FTW), but if you're using multiple technologies (e.g. Redis/MySQL/MongoDB/etc...) then this problem becomes a lot harder. Do you roll back all of them? How do you manage that? How do you test it? What happens if one of the projectors made an API call that you can't roll back? Hmmm, things just got very complicated.

### 3. What happens if one of the projectors has a temporary error?
Sometimes a projector error is temporary, e.g. a rate limited API. The current request fails, but the next one may go through just fine. Do you just accept that and allow processes to fail, forcing the user to retry and it MIGHT work this time? That's not a great user experience.

### 4. What if you need to launch a new projector that needs historical events?
Immediate works if you only care about events as they happen, but sometimes you'll need to play all events historically. Take a new projections, it needs all events to build up its data set. This means there's a period while it's replaying where it's eventually consistent, i.e. it'll eventually catchup, but it's not there yet. So no matter what, you'll need to handle eventual consistency.

### 5. What if you need to process events on a different service?
Ahh, the classic problem of distributed systems. How do you force another system to process an event immediately and . Sure you can make a synchronous call, but eventually one of those calls will fail, and what do you do then? Immediate consistency becomes lot harder once you're communicating with another service, even if it's one you control yourself.

## Reasons to use Eventual Consistency
How does eventual consistency stack up to the above problems? Quite well, let's take a look.

### 1. What happens if one of the projectors is slow?
This isn't an issue, because our our projectors are updated in a background process, they can take as long as they want. If we find out one projector in particular is going slowly and affecting others, we can simply move it into it's own process and move on.

### 2. What happens if one of the projectors has an error?
Again, pretty simple. If there's an error, report it and disable the projector. Once an event has happened, it's happened, so if one projector fails we don't need to roll back the events or the changes to other projectors. Instead we fix the projector, roll out a new release and let it catch up. Once you embrace eventual consistency, problems like this become a lot easier to tackle.

### 3. What happens if one of the projectors has a temporary error?
That's fine, we simply swallow the error and try again. We know the request will eventually get through, we just need to keep sending it. If it's a rate limiting issue, we can throttle the projector, slowing down the speed at which it's processing events. Again, moving that projector to another process makes this easier to manage.

### 4. What if you need to launch a new projector that needs historical events?
Not much to say here. Running a new projector is the same as running any other projector, it's assumed it's behind and it will eventually catch up.

### 5. What if you need to process events on a different service?
Yep, no issues here. Events are consumed by other services at their own pace. The producing service doesn't need to wait for them to handle the events, so it doesn't matter if they're running slow, or even that they're running at all.

## Choosing Immediate or Eventual
At this stage it should be clear that there's a tradeoff between the two. Immediate is easier to reason about, as it's an all or nothing operation, but it's open the door for lots of potential problems. Eventual on the other hand give you more freedom, but it makes immediate debugging harder. When deciding which to use, be sure to ask yourself how you'll handle partial failures, if they can happen, and you're using multiple technologies, then consider moving to Eventual Consistency.

> Protip: When running acceptance tests, make all your projectors immediately consistent, this makes it easier to spot errors during tests and makes things a lot less complicated to debug

Personally, I opt for Immediate Consistency when dealing with domain projections (i.e. projections required to validate domain wide business constraints). To do this, I store the events and the domain projection in the same datastore, wrapping the entire process in a transaction to make rollbacks trivial. For everything else I use mastercard, I mean Eventual Consistency, as it's makes building and maintaining the system easier to manage.

What about you, do you opt for immediate or eventual consistency? What kind of issues have you had and how have you solved them? Let me know in the comments!

