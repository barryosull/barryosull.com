---
title: "Event Granularity: Modelling events in event driven applications"
published: true
description: An exploration of event granularity, with some examples of how we can get it wrong, and figured out how to make it right.
tags: event driven, architecture
cover_image: https://thepracticaldev.s3.amazonaws.com/i/fyx1lmzg95nn3q36dthj.jpg
---
Interested in building an event driven application? If so there's one problem that will rear it's head pretty early; how big or small do you make your events?

# Event Driven Systems
Before we begin, let's give some background on Event Driven Systems. Event Driven systems don't make synchronous calls to each other, instead they broadcast messages, ie. "Events". Other services listen for these events and process them at their own speed. Say a service starts to slow down or crashes, other services will keep working as if nothing is wrong. Once the failing service is booted up again, it goes back to processing events and catches up to the rest of the system. It's a great way to build robust, distributed systems, such as a micro-service based architecture. 

![Event Driven Systems](https://thepracticaldev.s3.amazonaws.com/i/fo7b8dewfc1qyl358xda.jpg)

So if you're building one of these systems, how do you define your events?

# Modelling Events
When designing an event driven systems, we need to model the events that cause changes in that system. Events are used to broadcasts changes between all our systems/services, so they need to meet two criteria, they must be:

1. Expressive
2. Useful

In other words, they need to describe what they do, and contain information that's easy to consume. This is trickier than it sounds, especially if you're coming from a table driven, CRUD/ORM style of development.

# Granularity
For our team, the biggest problem was figuring out the granularity of events. How small/detailed should they be? Do we need an event for every value change? Or do we use large events that broadcast entire entities, such as the user object? These are questions of granularity, and in order to answer it, we had to understand the spectrum of granularity.

![Event Granularity](https://thepracticaldev.s3.amazonaws.com/i/s23rh9pgrp13h1rm6282.png)

When designing an event, you need to choose the right point on the spectrum. Too fine and the event isn't useful. Too coarse and they're not expressive. From our experience, there are really three ways you can get it wrong.

1) Too coarse
2) Too fine
3) A bit of both

## Too coarse
Coarse events are large and generic. They have a generic name and contain lots of data. However, the name doesn't convey a lot of meaning, so you have to check the data itself to understand what's actually happened. This means that your services have to listen to these events, and check the data within them, before they can figure out if they actually care. This is a sign you've bled some logic from your domain into your listeners.

### Coarse events
Take the event `AccountStatusChanged`, with the following schema.
```yaml
AccountStatusChanged
  accountId: 12
  status: "closed"
```    

This is a coarse event because it's not expressive. None of our processes care if the status changed generically, they care if it changed to a specific value. The marketing service cares if the account has "closed", while the billing service cares if the account has been "activated" or "closed".

### Fixing coarse events
Since these two value changes trigger different processes, it makes sense to model them as two discreet events.
```yaml
AccountClosed
  accountId: 12

AccountActivated
  accountId: 12

```    
These two events are far more useful, as they give context on what's actually happened.

### Why does this happen
Coarse events happen for two reasons:
1. The developer is pre-optimising by trying to reduce the numbers of events in their domain (we did this).
2. The events were created from UI mockups, rather than talking to the domain expert and figuring out what's actually important (we also did this).

## Too fine grained
Fine grained events are small and specific. They are typically well named but contain very little information. An event that is too fine grained isn't useful in and of itself, you have to combine it with lots of other events before you can decide what to do next. If your listeners listen to multiple events triggered by the same system at the same time, that's a good sign they're too fine grained.

### Fine grained events
Take the following events `CustomerFirstnameChanged` and `CustomerLastnameChanged` with the following schemas.

```yaml
CustomerFirstnameChanged
  userId: ...
  firstName: "Tim" 

CustomerLastnameChanged
  userId: ...
  lastName: "The Enchanter" 
```    
These events are too fine grained because no service cares if they happen independently. Our services only care about the customers's current name. They don't care if the first-name and last-name change at different times, that's irrelevant, they just care about the value as it is now. Our emailer service, for example, listens for both of these events, just so it can update the users name. That's it. There's no real reason for them to be separate.

### Fixing fine grained events
Based on the above, a much better event would be `CustomerNameChanged` with the following schema.

```yaml
CustomerNameChanged
  userId: ...
  firstName: "Tim" 
  lastName: "The Enchanter" 
  
```
Now our listeners only have to listen to one event to trigger work, much simpler.

### Why does this happen
Granular events happen when a developer tries to avoid coarse events and goes too far in the opposite direction. The root cause is the same though, they didn't talk to the domain expert to figure out what's actually important, instead they guessed and got it wrong.

## A bit of both
I know it sounds odd, but you can also design events that are both too coarse and too fine grained at the same time. These events have generic names with lots of data (ie. coarse), but they're not useful in and of itself and are always coupled with other events (ie. too fine grained). They combine the issues of too coarse and too fine grained into one lovely messy package. 

### Getting it wrong

```yaml
CustomerFirstnameChanged
  userId: ...
  firstName: "Tim" 

CustomerDetailsChanged
  userId: ...
  lastName: "The Enchanter"
  twitterHandle: "@enchantz247"
```
In the above `CustomerDetailsChanged` is the problem, it's both too coarse and too granular. 

It's too coarse because it contains too much information. The service that cares about the twitter handle changing doesn't care about the persons lastName. Yet, it has to listen to every one of these events and check that is was the twitterHandle that actually changed before it can continue. Pain in the ass.

It's also too granular; every service that cares about the persons name has to listen to both events before it can get the full picture. It suffers from the same problem as the fine grained example above, with the added problems of a coarse, less expressive event name. Another pain in the ass.

### Getting it right
Clearly we got our data boundaries wrong; we have data in the wrong event and our events are poorly named. Let's redefine them. 

```yaml
CustomerNameChanged
  userId: ...
  firstName: "Tim" 
  lastName: "The Enchanter" 
  
CustomerTwitterHandleChanged
  userId: ...
  twitterHandle: "@enchantz247"
```

These events are much easier to consume. Each service that cares about the name just listens for one event, same with services that care about the twitter handle. Much less complexity with a lot more clarity.

### Why does this happen
This happens when you need to handle a new piece of data in the system and do so poorly. You don't want to add a new event, so you just add that piece of data to an existing event, one that is already too fine grained. This is why it's important to fix event granularity problems sooner rather than later, it brings clarity and stops you making bigger mistakes. 

# So how do you find the sweet spot?

How do we find the right granularity for our events? Well, first we need to take a step back and actually talk to the domain expert. They already know the boundaries, they've been sending message for years and understand what's important and what isn't, what belongs together and what is separate.

![Event Storming](https://thepracticaldev.s3.amazonaws.com/i/anogja1gvusrqyql757l.jpg)

The best way to start is to spend some time [event storming](https://techbeacon.com/introduction-event-storming-easy-way-achieve-domain-driven-design) your domain with them. Figure out what messages (ie. events) are used internally in their department (ie. service) and what ones are used to trigger processes in other departments. It's important to note, you're not going to get it right in your first ES session, and that's ok. Do a couple of sessions with your domain experts over a couple of days, iterate on your model,you'll discover better events and avoid the problems outlined above. 

If this sounds like a lot of effort and you'd rather just start coding, keep this quote in mind. 

> Weeks of coding can save you days of planning.

In other words, it's better to discover the events at the beginning, rather than midway through when poorly defined events start getting in the way, causing pain, frustration and delayed delivery. It's not about big design up-front, it's about big discovery up-front. Hope you found this useful and happy coding!
