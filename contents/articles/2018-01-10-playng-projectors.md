---
title: Triggering Projections in PHP
published: false
description: ""
tags: 
---

## Triggering projectionists
In the last article we looked a projectionist, a system to play projectors and keep track of their position.

There's one thing missing, how do we trigger them? This is a little more complex that it sounds because it's about behaviour and implementation.

[Triggered]
Projectionist are triggered via the command line, usually a console command that is always running in the background.

# Key Beheaviours
In order for the triggering system to be useful, it has to meet a few criteria.
- When booting, projectors can play from any event, not just the latest.
- When playing, all projectors play from the same event
- We want to be able to play any projector from any point, not just the latest event
- We want updates to be quick, when an event fires, the projectors should play ASAP

So, keeping these in mind, let's look at what technologies we can use to solve this problem.

There are really two ways to run projectionists, one is "poll", the other is "poll and wait". 

# Poll
With polling, the projectionist constantly tries to play projectors. This means it queries the stream for each projector and sees if there's anything new. If there is the projector plays. If there isn't nothing happens. Then the system immediately tries again, constantly re-checking the stream in an infinite loop. (This is how you'd do it if you had a MySQL implementation of the EventStream BTW).

This is obviously very wasteful. It will work for small systems, but it does not scale and needlessly hammers your DB. This is why I prefer "poll and wait". With poll and wait, you connect to the stream and wait for new data. If you don't get any, you connect and wait again. The obvious answer here is to use a queue.

"Wait, this sounds really complex." That's because it is, or at least it was (ooooh, mystery). The above poll and wait system works, it works really well, but it was awkward to get running and required two different ways of playing events into projectors (MySQL first, then switching to SQS), it was also sequential, no way to run two projectors simultaneously. 

Sadly, this is trickier to implement than it sounds. If you want each projector to run at it's own speed, then you need a queue for each projector (difficult to manage). If that's sound infeasible (it is), then your other option (the one we chose) is to use a single queue for live events, popping of the latest event and passing it through to each projector in sequence (no parallelism). Even this solution has it's problems, you still need some way to play new projectors up to the oldest event in the queue, before switching over to the running queue. 

Thankfully there's a simpler answer, just use Apache Kafka. A Kafka implementation  allows each projector to query the same stream (1 queue) from any position and wait for new data (no constant polling). This means each projector is independent and can run at its own speed, allowing each projector to have its own projectionist in its own thread if needs be. Best of all worlds.

# Poll and Wait

[Drawing: Kafka queue impl, with multiple projectionists]

Seriously use Kafka, it will allow you to leapfrog us.behaviou

Now that we have a projectionist, how do we play it?

There are two sides to this problem. What is the behaviour we want, and how do we implement that behaviour?

How we did it
We went with a simple implementation. 

This is complex, different behaviours change the implemnetaiton drastically.

How the triggering system works depends completely on how you want projectors to update and behave.

MySQL implementation

Queue based implementation


Hybrid Queue and MySQL
This one is quite simple. You have a single queue for broadcasting events. You poll the queue and wait for an incoming event, if there's an event the projectionist is played, otherwise do nothin, then repeat.

Why do it like this?

This means you only need one implementation for event stream, rather than two.




* An improvement for playing, rather than making a call for each

Phew, this article has taken on a life of its own.

Ok, what are we discussing? That's right, the projectonist, how do we trigger it?

It's job is to listen for incoming and events and apply them to projectors, what's the best way to implemnet this?

Well, it all depends on how you want it behave, and how efficient you want it to be.

It comes down to two thing.
Efficiency and cost to implement (building and maintaining).

# MySQL
Run the projectionist in a constant loop, using MySQL as the implementation of the event stream.

Cost to implement: 2
By far the simplest to implement.

Efficiency: 1
Slower, lots of wasted SQL connections

# Smarter MySQL
Group the projectors by the last event they've seen, use one event stream when playing.

Cost to implement: 3
By far the simplest to implement.

Efficiency: 3
Slower, lots of wasted SQL connections

# Hybrid Queue and MySQL
Use a queue to trigger the projectionist to play, still use a using MySQL as the implementation of the event stream.

Cost to implement: 5

Efficiency: 5

This is the one we use currently, and it works a treat.


# Pure Queue
This is kind of the joke one, as it seems like a great idea but completely falls apart as soon as you think about it.

Cost to implement: 10

Efficiency: 4
You don't really gain anything. Sure, you no longer have the call to the DB, but you have to do this for each projector.
Not to mention you need to manage the queues as well and build them. 

This is by far the worst option and you should never try this one, it just isn't worth it.

# Kafka
This is by far the best solution. Kafka allows us to have all the benefits of the Hybrid Queue and MySQL system, while using just one technology.

Cost to implement: 4

Efficiency: 7

As you can see, this one is the clear winner.

