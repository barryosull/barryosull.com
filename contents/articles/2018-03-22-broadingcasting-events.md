---
title: "Broadcasting events in PHP"
published: false
description: "When it comes to building and playing projetors, how quickly should you process events and what are the trade-offs"
tags: event-sourcing
---

Blocking:
Immediate is blocking, and eventual is non blocking. Ie. With immediate you have to wait for all events to be processed before you can complete a request, with eventual you only care about storing the eve

Error Handling:

Standard development is sequential and transactional, so most developer opt for immediate straight away. This is fine, but it has it's own issues.

Personally, I use immediate for business logic projections (e.g. is this email address unique), and eventual for read models or slow processes (e.g. sending an email to a newly registered user).

With immediate, as soon as the event is fired, you update the projections. This seems simple but it has a couple of issues.
What happens when the projector fails to apply an event?
If there'sa bug in yiur

There are really two ways to run projectionists, one is "poll", the other is "poll and wait". With polling, the projectionist constantly tries to play projectors. This means it queries the event stream and sees if there's anything new. If there is the projector plays. If there isn't nothing happens. Then the system immediately tries again, constantly re-checking the event stream in an infinite loop. (This is how you'd do it if you had a MySQL implementation of the EventStream BTW).

This is obviously very wasteful. It will work for small systems, but it does not scale and needlessly hammers your DB. This is why I prefer "poll and wait". With poll and wait, you connect to the stream and wait for new data. If you don't get any, you connect and wait again. The obvious answer here is to use a queue.

Sadly, this is trickier to implement than it sounds. If you want each projector to run at it's own speed, then you need a queue for each projector (difficult to manage). If that's sound infeasible (it is), then your other option (the one we chose) is to use a single queue for live events, popping of the latest event and passing it through to each projector in sequence (no parallelism). Even this solution has it's problems, you still need some way to play new projectors up to the oldest event in the queue, before switching over to the running queue. 

"Wait, this sounds really complex." That's because it is, or at least it was (ooooh, mystery). The above poll and wait system works, it works really well, but it was awkward to get running and required two different ways of playing events into projectors (MySQL first, then switching to SQS), it was also sequential, no way to run two projectors simultaneously. 

Thankfully there's a simpler answer, just use Apache Kafka. A Kafka implementation  allows each projector to query the same stream (1 queue) from any position and wait for new data (no constant polling). This means each projector is independent and can run at its own speed, allowing each projector to have its own projectionist in its own thread if needs be. Best of all worlds.

[Drawing: Kafka queue impl, with multiple projectionists]

Seriously use Kafka, it will allow you to leapfrog us.

