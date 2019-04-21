---
title: Experiments
published: false
description: description
tags: legacy
cover_image: http://globalnerdy.com/wordpress/wp-content/uploads/2008/07/technical_difficulties_please_stand_by.jpg
---

Expirimentation

I've been working full time as a consultant/contactor over the last year (before that I did it part time on and off) focussing on fixing and improving legacy systems. One thing I've noticed, time and time again, is that most of the complications I'm fixing or navigating around are experiments that were left in the codebase.

The developer timeline

I was recently taking to a friend of mine who write simple programs to solve problems for himself, such as an invoice management system (he's self employed). He then said to me that he doesn't consider himself a developer though, because he just cobbles together code from tutorials until it works, he doesn't know anything about design. First of all, I told him that he is 100% a software developer, second, I would rather deal with his code than some of the "clever" code I've seen, and here's why.


Accidental Complication
JBrains has an excellent talk on this topic[Insert link to talk here]. Everytime we commit one of these experiments to the codebase were are potentially introducing accidentual complication. 

Lava Layer Anti-pattern
Talk here about lava layer.

When we learn how to program
When people learn to program, they start by copying code that they know works. If you're using a framework you'll copy the code examples and then tweak them until your system works. 

Why do we experiment? 
Eventually you'll get to a point that these systems become unmaintainable, or you get the niggling feeling that you're not doing things right. That's when you move onto experimenting.

Trying to get better
This is when things get dangerous. When we build something new, we try out a new technology or pattern, because why not? 

The expert beginner


The expert
This is when you're no longer trying to prove anything

So that's why I want to talk about experimneting, trying out new technologies, techiques and patterns.

We are really bad at this, and I mean spectacularly so. Most of the problems we face today are yesterdays solutions. 

This means we never get good at experimenting with new ideas, so we double down on any of the ideas that we do try.

I've made these mistakes, I look back at the experiments I left in the code I worked on and I shudder. The thing is, this is how I got better, it's why I am where I am today, so experiments aren't bad, infact they are necessary.

I think we need to discuss experimentation in software, and I have a few ideas of my own on the matter.

I worked on a system where the developer had tried 10 different experinments. Some were complex, others were simple, but overall all these experiments overlapped each other and got in the way of understanding what was going down. I ended up gutting the codebase, removing 40% of the code leaving the same functionality (with tests).

## Managing experiments
The job of improving the team lies on the manager. It's their responsibility to ensure that the team has the skills needed to do the job. So the manager should have an active hand in planning, executing and evaluating experiments. They need to work with the entire team to do this.If they don't view the system holistically then that is impossible.
If they don't, then developers will natually try out new techniques

## Be upfront and deliberate in the experiments you plan to run:
When building a new product for a client, I sat down and designed the architecture of the system, how I was planning to build it.

## Acknowledge experiments and carve out time for them
Testing out an idea takes time, and that's fine. It's ok if you think a piece of code is messy and you wan't to try out a pattern (small)

## Don't commit failed experiments
All because the code runs doesn't mean the experiment was a success. Introducing new concepts always comes with a cost, so you have to be honest on whether the benefit of using the pattern is worst the cost.

## You don't always have the right answer
As I progress as I developer I'm realising that I no longer have anything to prove. I.e. I'm no longer trying to prove I'm a smarty pants developer that knows everything and always has the right answer. 

## Review experiments with others
This is something that baffles me in hindsight. Most of the time we as developers will try out experiments, and WE WON'T DISCUSS THEM WITH OTHERS. We just add them to the codebase and we're done. This is madness, and it leads to most of the problems I deal with day to day.

## Adding new experiments:
- Specification pattern
- Moving to CQRS

Developers are going to do it anyway
The main argument I've heard against this is that you're taking time to do all these experiments when you should be writing code. Well here's the thing, if you don't manage it then the developers are going to do it anyway. Except now there's no oversight, so any issues and failures will get ignored until the system is a giant ball of mud, again.

Scratch refactoring

These are my thoughts on experiments, if anyone has anything throughts of links to content on the topic, please let me know in the comments!



