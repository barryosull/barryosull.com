---
title: Legacy Contexts: Patterns for dealing with Legacy
published: false
description: description
tags: legacy
cover_image: http://globalnerdy.com/wordpress/wp-content/uploads/2008/07/technical_difficulties_please_stand_by.jpg
---

I have not written in a while, mainly because of how busy my life has been. So I thought I'd start writing again on a topic that I deal with daily, Legacy web systems. I was spurred to write this based on my experience at DDD EU this year. Eric Evan gave a great talk on expanding DDD, with a fascinating section on different types of Bounded Contexts, some legacy, and some strategic for dealing with legacy. This inspired me and I grabbed one of the many white boards and started a session on "Patterns for dealing with Legacy". Over the course of two days, we produced this.

(https://i.imgur.com/MVJ16ta.jpg)[Patterns for dealing with Legacy]

The above was the result of many developers collaborating on legacy patterns, and I have to say, I was very happy with the end result. I know it's not the best photo, but there's a lot to it and that's why I'm writing this article, I want to do a deep dive into the concept expressed above, namely Legacy Contexts and Patterns for dealing with them.

## What is Legacy
To start, let's define Legacy. When we think of legacy, we usually think of Cobal and old banking systems, but that's just one specific example. A broader definition would be any computer system that is actively in use, valuable (otherwise why is it active?) and considered outdated in someway. I.e. they enable the business and they're functional, but they're tricky to change, either due to the hardware or the software. When you think about it, this comprises most active systems, especially user facing applications. As such, Legacy is part of what we deal with day to day. So what can we do about it? Well, before we explore techniques we first have to understand what we're dealing with as not all Legacy is the same.

## Types of Legacy Contexts:
We frequently joke that all legacy systems are uniquely bad. This is true in a way, no two are really the same, either due to the domain they're modelling or the way they're implemented. That's one of the issues with the term "legacy" it's too broad and acts as a catch-all for outdated systems. In order to make the term useful, we have to dive down into the taxonomy of legacy, categorising the different patterns found in outdated systems. Using these we are better able to understand our legacy systems and how to deal with them. 

After all, you can't get where you're going if you don't know where you are. 

### Ball of Mud Context
The quintessential legacy model. When we think of legacy we're usually think of Balls of Mud. We typically say that the codebase is a mess but what we really mean is that it lacks a conceivable architecture. Every part of is different and knowledge from one one part of the systems rarely translates to other parts. In short, you can't keep a model of the system in your head, it's just too chaotic. 

https://exceptionnotfound.net/big-ball-of-mud-the-daily-software-anti-pattern/

What's interesting is that "ball of mud" is an subjective term. It's based entirely on the ability of the observer to understand and categorise systems. The less knowledge they have, the more likely they are to consider something a ball of mud. Therefore the more we know about legacy patterns, the less likely it is we're dealing with a ball of mud.

Now this might sound controversial, but I think it's hard to actually create a true ball of mud. You have to be consistently inconsistent, which is nearly impossible, we always fall into patterns. That's why I consider the following patterns to be so important, most of what we call "ball of mud" are actually subsets and combinations of the below patterns. Once we understand these, we no longer have a ball of mud, we have something with a semblance of structure.

### Quaint Context
Sometimes a system is just old, but not broken. A quaint context is a system that is built on old technology using an old language. There systems are full of older design patterns, old naming conventions and not quite clean code. They're not bad systems though, it's clear they work, they're just not up to speed with how system design and technology have progressed. Think of them like a relative that only communicates via post rather than instant messaging. They're old fashioned, yet it works and is sort of endearing.

They're not quite ball of mud, they have a structure, and if the developer understands the language and the connected systems, they'll happily work away maintaining it. This only becomes a problem is when you can't find those people. Again, "Ball of Mud" is subjective.

### Patchwork
Patchwork contexts are systems that have had functionality patched into them progressively. There was a framework/architecture at the some point. However as time went on and changes were required they started patching in the code they needed, rather than restructuring the system for clarity. 

These differ from balls of mud in that the patch points are clear, it's obvious where the functionality was layered on top of the existing system. Overall you can still navigate them and understand what's going on. Keep patching them however, and like a piece of cloth, they will will fray, until you end up with a ball of mud.

### Lava Layer

We originally called this an anti-pattern in the image above, but I've realised it's actually a subset of legacy, one that is a little messy, but not quite a ball of mud.

(http://mikehadlow.blogspot.com/2014/12/the-lava-layer-anti-pattern.html)[Good write up]

### Mad Designtists Lab
Similar to a Lava Layer, except the layers are focussed on experiments that now liter the codebase.
This is the unfocused version of a Lava layer, lots of developers wanted to experiment and grow their skill set, but the codebase is left with the legacy of all those experiments. They're part of the codebase and none of them are consistent. 
They tend to be experiments in design patterns, with simple compontents over architected to the point of needless complexity.
These sytems are usually a symptom of developers that want to get better, but don't know how, so they try out every pattern they read about.

A Mad Designtists Lab can very easily turn into a ball of mud. Every experiment that's commited obfuscate the architecture, until eventually any structure or clarity is devoured by a layer of overly complex randomly applied design patterns. 

### Combinations:
Sometime you'll get codebases that are combination of the above

### Dealing with Legacy
Now that we've covered the some Legacy patterns, we're ready for the next stage, the patterns for dealing with Legacy. This part is still to come, so expect it in the future.