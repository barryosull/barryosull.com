---
title: title
published: false
description: description
tags: tags
cover_image: http://globalnerdy.com/wordpress/wp-content/uploads/2008/07/technical_difficulties_please_stand_by.jpg
---

Let's talk about Projectors. The concept is getting more popular, but at it's simplest, a projector is something that takes in a stream of events and does some work on them, projecting them into whatever shape or operation is needed.

So this is my attempt to talk about the concepts and problems we ran into when we started working with projectors day to day.

There are three key concepts here
1. Releasing a new projector
1. The different types of projectors and how they behave
2. What happens when projectors fail

# Releasing projectors
When you release a projector, it's not as simple as 
There are three stages in the lifecycle of a projector, and I'm going to go through each of them and why they exist.
1. Play
2. Boot
3. Retire

## 1. Play
The most basic one, a projector must be able to play events. This is the bread and butter of projections; when a event is triggered, the projector will process that event. This is called "playing" a projector.
Usually you'll have a process that manages this and keeps track of where each projector is in the event stream, I call it a "Projectionist".
When a collection of projectors are played, the projectionist looks at where they were last and tries to play them forward.
This is all very quick for active projectors, but it can be a bit of a problem for new projectos, which have to play though the entire event steam to catch up, that
s why we have a separate booting process.

## 2. Boot
When a projector is started from scratch, it has processed no events.
The goal of the boot stage is to prepare a projector for release. 
In the case of a standard projector, this means it plays from the start of the event stream all the way to now. 

Booting should always happen during the deploy process, and it's best if it's the last process before making code live.
Once all projectors has been `booted`, you can safely deploy the new code, letting the standard `play` system for projectors take over.
To users of the system it's seamless, any new projectors are up to date with the latest events and the change over is effortless.

## 3. Retiring 
Of course, after a while, you'll no longer need a projection and you'll want 

Retiring a projector involves two operations.
1. Delete all data stored in the projections controlled by the projector.
2. Delete the record keeping track of the projectors position.

You should only retire a projector once you're confident you won't need to use it again.
I would 


A key thing about projectors is that they must be keep track of where they are in the event stream. This is how they ensure they don't play events twice and that they're always able to start from the right position.

# Run modes
Ok, now that we've got that out of the way, let's talk about projector run modes. You see, not all projectors run the same way, they'll actually have different lifecycles.

In our experience, there are three types of projectors
1. Run from Beginning
2. Run from now (what Laravel are calling "Reactors")
3. Run once 

1. Run from Beginning
This one is pretty simply, start at the oldest event and run through all events.

Run from now
This projector should




|
|---|---|---|---|---|

Handling projector failures
Now that we understand how they work and the different modes, we can ask a difficult question. What happens when a projector fails?
Well, it all depends on the mode of the projector.

All of the above is independent of projector versioning, that is a completely different problem.