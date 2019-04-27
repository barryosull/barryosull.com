---
title: Experimenting in code
published: false
description: description
tags: legacy
cover_image: http://globalnerdy.com/wordpress/wp-content/uploads/2008/07/technical_difficulties_please_stand_by.jpg
---

I've been working full time as a consultant/contractor for the last year (before that I did it on and off part-time) focusing on fixing and improving legacy systems. These systems are typically hard to navigate and understand, thus making them expensive to change. In my experience there are two main reasons these systems get into this state:

1. The code has no design 
2. The code has experimental design

For me number 2 is worse and causes more problems, and that's what I'm going to talk today.

What do I mean by experiments? An experiment is any piece of code were the developer was clearly trying out a new idea. Be it a new technology, a library or a design pattern, these pieces of experimental code end up littering the codebase and obfuscating the actual intent of a piece of code. 

This should come as no surprise, we've all seen it and we've all done it, it's how we get better (or add a new technology to our CV). The problem isn't the experiments themselves, it's the fact they're left in the codebase. This is a systematic problem in software development, constantly rearing it's ugly head, messing up projects and causing headaches for developers and business owners. Why does this happen and what can we do about it?

Before we dig into those questions, let's have a look at the problems causes by experimental code.

## The problem with experimental code
It's not enough to say "it causes problems", if we want to actually address this we have to be able to explore and explain the kinds of issues this code creates. Looking at it from a problem space perspective, the first issue is that of accidental complication.

#### Accidental Complication
Every time we commit an experiment to the codebase we are potentially introducing accidental complication. This is complication that didn't have to be there, you didn't add it intentionally, but there it is, thus it is accidental. Some complication are necessary, like using the right design pattern, it's add more to the codebase, but if offsets this cost by increasing understanding. 

Accidental complication does not increase understanding, it is an obstacle that you must navigate before you can understand what's actually going on in a piece of code. It doesn't add anything, it's just getting in the way. Worse, it's inconsistent. When we try out a new idea we usually make mistakes, which we don't repeat, so any knowledge you gain about one area of the system will not transfer elsewhere, the accidental complication will always be different. This is the main reason why we can't estimate reliably, we never know how much accidental complication we'll uncover or how much it will impede our work. 

JBrains has an [excellent talk on this concept](https://www.youtube.com/watch?v=WSes_PexXcA).

So that's the issue from a problem perspective, but how does this manifest in the solution space?

#### Lava Layer Anti-pattern
If a software system is profitable it will be long lived, and any long lived software inevitably turns into a layered mess of patterns, libraries and technologies. It becomes stratified with these concepts, some cutting through the entire codebase, others living in their own little module.

To see why this happens, let's go through an example.

Say your SQL queries are getting messy and difficult to manage, so someone decides to use an ORM for one part of the system. A few years later that ORM is considered out of date, so someone else decides to use the newer, sexier ORM! Actually wait, that's not good anymore, ORM is out, MongoDB is in. And round and round it goes, until the entire codebase is stratified with different "experiments" in data storage technologies and techniques.

This patterns comes into play whenever someone decides to try out a new idea or technology in a feature, and then stops once the feature works. They don't migrate the entire system over to the new paradigm, it stays as it is. So now we're left with a system that has two ways of doing things when it used to have one. 

All these layers introduce accidental complication, so it's no wonder we have such difficulty with lava layers systems. And as the system gets more stratified and difficult to understand, we try to fix it by applying yet more patterns and ideas, accelerating it's descent into a ball of mud.

Here's a good write on the concept that does into [more detail](http://mikehadlow.blogspot.com/2014/12/the-lava-layer-anti-pattern.html).

This is clearly a big problem and it affects every piece of software I've ever worked with professionally, can we figure out why it happens?

## Why this happens
When we initially learn to program, we start by copying code that we know works. If we're using a framework we'll copy the code examples and then tweak them until the system works. 

Eventually we'll get to a point that these systems become unmaintainable, or we get the niggling feeling that we're not doing things right. That's when we move onto experimenting.

When developers start out, they 

They have no concept of design or structure, and why would they? They're just getting to grips with the basic.

After a while the developer realises there are holes in their skillset, and they need to learn about design in order to progress, or they think they need to know certain skills to keep themselves employed (CV driven development).

The developer timeline
To understand why this happens, we need to discuss the developer timeline.

When we start coding we just hammer code together until it works. This strategy works for a while, so we keep doing it, until we reach a p

I was recently taking to a friend of mine who write simple programs to solve business problems, such as an invoice management system (he's self employed). He then said to me that he doesn't consider himself a developer though, as he just cobbles together code from tutorials until it works, he doesn't know anything about design. First of all, I told him that he is 100% a software developer, second, I would rather deal with his code than some of the "designed" code I've seen, because most 



When we learn how to program


Why do we experiment? 


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

I worked on a system where the developer had tried 10 different experiments. Some were complex, others were simple, but overall all these experiments overlapped each other and got in the way of understanding what was going down. I ended up gutting the codebase, removing 40% of the code while maintaining the same behaviour.

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



