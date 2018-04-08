---
title: "Immediate vs eventual consistency"
published: true
description: "When it comes to building and playing projectors, when should you process events and what are the trade-offs"
tags: event sourcing, architecture, event driven
cover_image: http://barryosull.com/images/1e92472a-f6d6-444a-ab64-5ba3d659820c.png
---
In the [last article](https://barryosull.com/blog/projection-building-blocks-what-you-ll-need-to-build-projections) we looked at projectors, the backbone of any CQRS/Event Driven system. This article was originally meant to be about implementing projectors, but I realised there was an important question to answer first, one that would shape the solution, "When do we project the events, now, or later?". Turns out this question has far reaching effects, so it's important we dig into it before moving onward.

# Immediate vs Eventual Consistency
When it comes to projectors there are two choices, immediate or eventual consistency. With immediate, events are processed by projectors as soon as they happen. With eventual, events get processed in a different process at a later time (usually a split second later). 

Immediate is an all or nothing operation, if anything goes wrong then the entire process is halted. No events are stored and no events are processed. Eventual is a staggered operation, once the events are stored each of the projectors will process them at a later time (and potentially fail).

## Reasons to not use Immediate Consistency
From the above you may think that immediate is the obvious choice, it seems simpler and has less moving parts. Well, that simplicity is an illusion. It turns out immediate is far more complex and the following questions will illustrate why.

### 1. What happens if one of the projectors has an error?
Say a projector fails and throws and exception, what do you do? The ideal solution is to roll back all your changes, ie. act like it never happened. This is easy enough if you're using a single DB to store everything (transactions FTW), but if you're using multiple technologies (e.g. Redis/MySQL/MongoDB/etc...) then this problem becomes a lot harder. Do you roll back all of them? How do you manage that? How do you test it? What happens if you make two API calls that you can't roll back? Hmmm, things just got very complicated.

### 2. What happens if one of the projectors has a temporary error?
Some errors are temporary. Say you have a projector that connects to an API that's rate limited. 

- A user makes a request causing an event
- The projector tries to process that event
- It makes an API request, you're over the rate limit, so it fails
- Another user makes a request causing an event
- The projector tries to process that event
- It makes an API request, you're under the limit, so it goes through

How do you handle this? Do you just accept it and allow processes to fail? Do you force the user to retry the request and hope it works this time? That's not a great user experience and will definitely annoy people.

### 3. What happens if one of the projectors is slow?
Say one of the projectors performs an expensive process, like connecting to a slow external service (eg. sending an email). With immediate consistency we have to wait for it to complete before we can let the user continue. Worse, if we're using transactions to ensure data integrity across a domain (e.g. email address is unique), then you're potentially slowing down other processes, not just this one. These become bottlenecks that affect the entire system.

### 4. What happens when you launch a new projector in a running system?
Even if you opt for immediately consistent projectors, you'll still need some way to play historical events into projectors, otherwise you'll be unable to launch new ones. While new projectors are spinning up, they are not consistent with the live system, but they will be. You can architect the process so that you only release the code when all the projectors have finished (we did this, worked really well), but even so, to do this you had to build the core of an eventually consistent system.

### 5. What if you need to process events on a different service?
Ahh, the classic problem of distributed systems. Processing events within a single service can get complicated, but it's nothing compared to immediately processing events on a different service/server. The laziest solution is to force other services to process the events immediately via a synchronous call, but now you've coupled yourself to that system; if it goes down; you go down, and what do you do then? Immediate consistency becomes a lot harder (and next to impossible) once you're communicating with another service, even if it's one you control yourself.

## Reasons to use Eventual Consistency
Now that's we've seen the problems caused by forcing immediate consistency, let's look at how things fare when we take an eventually consistent approach.

### 1. What happens if one of the projectors has an error?
That's fine, if there's an error, report it, maybe disable the projector (depends on the error). Once an event has happened, it's happened, so if one projector fails we don't need to roll back the events or the changes to other projectors. Instead we fix the projector, roll out a new release and let it catch up. Once you embrace eventual consistency, problems like this become a lot easier to handle.

### 2. What happens if one of the projectors has a temporary error?
Again, pretty simple. We simply swallow the error and try again. We know the request will eventually get through, we just need to keep sending it. If it's a rate limiting issue, we can throttle the projector, slowing down the speed at which it's processing events. 

### 3. What happens if one of the projectors is slow?
This isn't an issue at all. Projectors run in their own background process, so they can take as long as they want. If we find out one projector in particular is slow and is affecting others, we can simply move it into it's own process and move on. Nice and easy.

### 4. What happens when you launch a new projector in a running system?
Not much to say here. Running a new projector is the same as running any other projector, it will play though the events until it eventually catches up. This simply isn't a problem anymore.

### 5. What if you need to process events on a different service?
Yep, no issues here either. Events are consumed by other services at their own pace. The producing service doesn't need to wait for them to handle the events, so it doesn't matter if they're running slow, or even that they're running at all.

## Immediately consistent views
At this point I've hopefully convinced you that eventual is better, but there's still one problem to address, one you're probably asking right now, "What happens when you need views to be immediately consistent?". 

Let's take an example, say you've processed a request to add an item to a cart, and the user is redirected to the cart page, what do you do if the cart is rendered without the latest item because the cart projector is running slow? Simple, you fake it. You render the page as if the item is actually in the cart, even if the view says it isn't. 
 
This isn't as crazy as it sounds, in-fact most apps do this all the time and you barely notice. Have you ever posted to Facebook, seen your post appear, then refreshed the page and noticed your post isn't there? They were faking it. This fakery is mostly done on the client side, and it's made even easier by the likes of the [reflux](https://github.com/reactjs/redux). This pattern is more commonly know as an optimistic UI, [here's an article on the concept](https://uxplanet.org/optimistic-1000-34d9eefe4c05).

What I'm trying to say is that it really isn't a big deal, apps do this all time and it's very easy to implement, so there's really no reason not to do it.

## Choosing Immediate or Eventual
At this stage it should be clear that there's a trade-off between the two. Immediate is easier to reason about, as it's an all or nothing operation, but it opens the door to lots of potential problems, especially once you move to a distributed architecture. Eventual on the other hand gives you more freedom and scalability, but it makes debugging harder. When deciding which to use, be sure to ask yourself how you'll handle failures. If you're using multiple storage technologies or APIs, then you should seriously consider moving to eventual consistency.

> Protip: When running acceptance tests, run all your projectors as immediately consistent, this makes it easier to spot errors during tests and makes things a lot less complicated to debug.

Personally, I opt for immediate consistency when dealing with domain projections (i.e. projections required to validate domain wide business constraints) and eventual consistency for everything else.

What about you, do you opt for immediate or eventual consistency? What kind of issues have you had and how have you solved them? Let me know in the comments!

