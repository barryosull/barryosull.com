---
title: title
published: false
description: description
tags: tags
cover_image: http://globalnerdy.com/wordpress/wp-content/uploads/2008/07/technical_difficulties_please_stand_by.jpg
---

Let's talk about Projectors. The concept is getting more popular, but at it's simplest, a projector is something that takes in a stream of events and does some work on them, projecting them into whatever shape or operation is needed.
So this is my attempt to talk about the concepts and problems we ran into when we started working with projectors day to day.

# Run modes
Let's start simple, let's talk about the different types of projectors and how they behave. You see, not all projectors run the same way, and in my experience there are three types.
1. Run from Beginning (standard projector)
2. Run from now (what Laravel are calling "Reactors")
3. Run once (special case)

## 1. Run from Beginning
This one is pretty simple, start at the oldest event and play forward from there. These projectors will play through all historical events, and then continue processing any new events that occur. Most projectors will work like this.

## 2. Run from Now
Some projectors don't want historical events though, they only care about events that happen after they're released. These are rarer than the above projectors, but they still exist.
For example, say you wanted to release a new welcome email service. It sends welcome emails to new users, but you only want it to send email to newly created users, you don't want to send email to existing ones. That's where this projector mode comes in, by only playing forward from now, you ensure only new users are event processed, so only those users will get emails. Nice and simple.

## 3. Run Once
The rarest projector type. I've only had to create four of these, but they were essential to adapting a living system. These projectors play forward from the oldest event, but they only run once. Once they're run, they'll never run again. Why would you need this? Usually for difficult migration issues. Say you need to update a domain model so that it has new data. Now, it's easy to add this change to the code so newly created objects have the data, but what about historical objects? Well, the run-once mode is very useful here. You can write a projector that back-fills the missing data from historical events, then once that's complete you release the code and let the standard code take over, creating new objects with the data. A difficult problem made relatively easy. 
You could probably write an entire chapter here, but think of these as migrations, where it upgrades existing data structures in prep for a release.

# Projector lifecycles

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

## 3. Retire 
Of course, after a while, you'll no longer need a projection and you'll want to remove it from your system.
Retiring projectors is pretty simple. You'll need someway to remove the projection data, and the record keeping track of the projectors position. With that done, the projector is now officially retired.

Retiring a projector involves two operations.
1. Delete all data stored in the projections controlled by the projector.
2. Delete the record keeping track of the projectors position.

### When to retire
You might think you should retire projectors immediately once they're no longer used, but that's not a great idea. Imagine you have to rollback you system

You should only retire projection based projectors, any projectors that have sideeffects on the command side should be left. in, so you don't run the risk of rerunning them when you revert a deploy.

A key thing about projectors is that they must be keep track of where they are in the event stream. This is how they ensure they don't play events twice and that they're always able to start from the right position.

# Projector failure
So, I've got some sad news, at some point your projectors will fail. I know, it should never happen, but it definitely will, so you should be prepared for these failures.

How you handle a projector failure depends on on whether it's booting as part of a release, or it's being played as part of a live system.

When a projector fails while being played, it should record the failure and then stop playing. All other projectors should keep going as normal, there's no point in one failing projector bringing down all of them.
The failure should be logged and send an alert to your team so you can start fixing it. Once fixed, deploy the code and boot the broken projecto. It should play forward from the last event it successfully processed. If it's fixed, then it will successfully play the events and you'll be able to do a release. This makes fixing projectors a breeze.

If a projector fails during boot, that's a different story. When a projector fails during boot, it should stop the boot process immediately and mark itself as broken. It should also mark any other projectors played with it as stalled.

In the next boot process, it will attempt to play any broken and stalled projector

|
|---|---|---|---|---|

Handling projector failures
Now that we understand how they work and the different modes, we can ask a difficult question. What happens when a projector fails?
Well, it all depends on the mode of the projector.

All of the above is independent of projector versioning, that is a completely different problem.

TODO:
Review language system. 
    Eg. Play and Boot, `play` is too generic a concept.
    Explore turning modes into strategies.