---
title: Managing projectors is harder than you think
published: false
description: Building a robust projector system can lead to a few surprises, this is how you manage them
tags: projectors, CQRS
cover_image: http://barryosull.com/images/0735d1e5-ab3d-4bc2-90e3-2b885f5f260f.jpeg
---
Let's talk about Projectors. At it's simplest a projector is something that takes in a stream of events and does some work on them, projecting them into whatever shape or operation is needed. Like anything though, there's more to it than that, lots more. That's what this article is, my attempt to discuss the complications and problems you will run into while working with projectors day to day.

# Run modes
Let's start simple, let's talk about the different modes of projectors and how they behave. In my experience it boils down to three modes.
1. Run from beginning (standard projector)
2. Run from now (what Laravel are calling "Reactors")
3. Run once (special case)

## 1. Run from Beginning
This one is pretty simple, start at the first event and play forward from there. These projectors will play through all historical events, and then continue processing any new events that occur. Most projectors will run in this mode by default.

## 2. Run from Now
Some projectors don't need historical events, they only care about events that happen after they're released. For example, say you wanted to release a new email service. It's job is to send welcome emails to new users when there is a `UserRegistered` event, but you only want this to happen for new users, existing users shouldn't receive anything. 

This projector mode only processes `UserRegistered` events created after the projector is released, so only new users will get emails. Nice and simple. This projector mode is rarer than "Run from beginning", but it's still a must have.

## 3. Run Once
The rarest projector type. I've only created four of these, but they were essential to adapting a living system. These projectors play forward from the first event, but they only run once. Once they're run, they'll never run again. 

Why would you need this? Well we used them for difficult migration issues. Say you need to update a domain model so that it has new data. Now, it's easy to add this change to the code so newly created objects have the data, but what about historical objects? This is where run-once mode comes into play. You write a projector that back-fills the missing data from historical events, at deploy you run it, adding the missing data, then on release it stops running and never runs again. Now all your data is up to date. Think of them as migrations that upgrade existing data structures via events, usually in prep for a release.

How that's we've looked at the different modes, let's discuss the lifecycle of a projector, and see how modes come into play.

# Projector lifecycles
There are three stages in the lifecycle of a projector, from start to finish they are.
1. Boot
2. Play
3. Retire

We're going to start with number 2, play, because it's how most people are used to thinking about projectors.

## 2. Play
The most basic task, a projector must be able to play events. This is the bread and butter of projections; when an event is triggered, the projector will process that event. This is typically called "playing" a projector. Usually you'll have a process that manages this and records the position of each projector in the event stream.

This is all very quick for active projectors, but it can be a bit of a problem for new projectors, which have to play though the entire event steam to catch up, that's why we have a separate booting process.

## 1. Boot
The goal of the boot stage is to prepare a projector for release. When a "Run from Beginning" projector is introduced to an app, it will need to play all the events in the log. This process takes time, and if you've pushed the code live, anything reading the data will get old, possibly dodgy, data. E.g. Reports with incorrect numbers. To solve this you should have a distinct `boot` process that preps projectors before release.

Booting should only happen during the deploy process, and it's best if it's the last step before pushing code live. Booting only affect new projectors, i.e. ones without a position record. Once all projectors has been `booted`, you can safely deploy the new code, letting the standard `play` system for projectors take over. To users of the system it's seamless.

## 3. Retire 
Of course after a while you'll no longer need some of your projectors and you'll want to remove them from your system.

Retiring projectors is pretty simple and involves three steps:
1. Delete the projector code
2. Delete all data stored in the projections controlled by the projector
3. Delete the record keeping track of the projector's position

BTW, for steps 2 and 3 I'd suggest using migrations, it's explicit and you have a full historical record of changes.

### When to retire
You probably think you should retire projectors all in one go as soon as you don't need them, but that's not a great idea. Imagine you have to rollback your system to a deploy with a retired projector. Before the rollback can complete it would need to rebuild the projector, which takes time, time you don't have if the rollback is critical. This is why I usually leave leave step 2 and 3 for a few days, only deleting the code from the repo to start.

Another tip, you should only completely retire side-effect free projectors. Projectors that trigger side-effects (e.g. sending emails) should not have their position record deleted (step 3), otherwise you run the risk of accidentally replaying them, triggering the side-effects again. It's safer to delete the code and projection data (steps 1 and 2), but leave the actual position record alone.

# Projector failure
I've got some sad news, at some point your projectors will fail. I know, it should never happen, but bugs are inevitable and it's wise to prepare for them. Some examples I've seen, invalid event schemas, missing DB columns, missing data in DB (bad assumptions about event order), broken external service, etc... you get the idea.

Projectors can fail during the boot or the play processes, and how you handle failure depends on which.

## Boot failures
When a projector fails during boot, it should stop the boot process immediately and mark the projector as `broken`. If there are other projectors being booted at the same time, they should be marked as `stalled` (more on this soon). Then your team should be alerted of the issue, probably via a bug tracker. It goes without saying that "deploy" process should fail as well.

Now it's a case of fixing the broken projector and triggering a new deploy. When boot runs the second time around, it should attempt to play both the `broken` and `stalled` projectors forward from their last position. If the bug was fixed, then they'll all boot successfully and you can deploy the code. Done. If not, then repeat the process until it's all fixed.

## Play failures
Boot will catch most bugs, but sometimes you'll encounter an error while a projector is running as part of the live system. If a projector fails while being played, it should record the failure, mark the projector as `broken`, alert the team, and then stop playing that projector. All other projectors should keep going as normal, as there's no point in one failing projector bringing down the entire application.

Once the projector is fixed, trigger a deploy, which will cause boot to attempt to play the `broken` projector forward. If it's fixed, then it will successfully play the events and you'll be able to do a release. 

## Conclusion
To summarise, projectors are more complex than people think. If you want to build a stable, maintainable projector system, keep the following in mind.
1. Run modes
2. Lifecycle
3. Failures
If you have a handle on all three of these, then you're set. You'll be able to deploy and manage projectors with ease, fixing issues when they arise, with minimal issues for you or your customers.

I've built a projectionist system to solve the above problem, but it's not ready for the limelight, it's more of an experiment in distilling the above down into a single system. Feel free to have a look though! https://github.com/barryosull/the-projectionist

And with that, happy projecting!