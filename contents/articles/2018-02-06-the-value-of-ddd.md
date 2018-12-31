---
title: "The value of DDD"
published: false
description: ""
tags: management, design-patterns, growth
cover_image: http://barryosull.com/images/ff21e4a8-0140-4d27-9172-46e19b3dcd33.jpg
---


Points to make

What is DDD
Domain Driven Design (DDD) is an approach to software development that focuses on understanding the needs of the business, rather than the software.
In software development we get wrapped up in our solutions, our implementations. We think of everything in terms of technology and eventually we stop seeing the problem and can only see our solutions.
This leads to shitty software.

DDD is about understanding the business, rather than focussing on technology.

We as developers tend to jump to straight to implementation, to technology, when solving a problem. You need a webapp? React with This causes large problems are it's a premature optimisation, how can you "solve" a problem if you don't really understand it?


What it stops

What it enables

How to get started

Domain Driven Design
DDD is an approach to software development where the focus is placed on the domain, rather than potential solutions. Ie. What does your business want to do, rather than how does it do it. It's about understanding the problems and process, removing the noise of technology.

Where did DDD come from?
DDD came about because developers noticed a repeating pattern in software development. We were building software that was hard to change and did not represent the actual business processes.

DDD is about writing code that actually models the process, putting the goals of the business process at the forefront, rather than the technology we use to implement it.

Let me give you an example
In standard development, if I ask you to make me a blog, you'd ask some rudimentary questions, then immediately start mapping that to HTML, Database tables 

The value of DDD
DDD aims to solve a major problem with software development, 

Shared Language
Business  

The problems with standard development

The business and the developers use different language
Your developers are the ones modelling your process, ie. they're the ones building a digital representation of your business, so that it can be automated. Now, have you ever noticed that the business and the developers use different language when referring to the same things? You're talking about "reports", they're talking about "databases", and neither of you is really getting across the problem. This is one of the core problem with software development. We, as developers, have our own language for talking about our solutions, and

Developers speak their own language
Have you ever talked to a developer and they start using technical terms right of the bat? You're there to talk about a business problem and they immediately start talking about databases, files and servers? I bet you have, and there's a very simple reason. No, it's not that we want to sound clever and are trying to confuse you, it's because that's how we see the system. We conflate the implementation (the technology) with the domain (the business process). Ie. We don't view them as two separate things, we think our solution is the process, not just a representation of it.
This causes major problems. First off, we can't communicate with the business, if you say one thing and we say another, how do you know we're actually talking about the same thing? Second, the business process is tightly coupled to the implementation, e.g. our business logic is tightly coupled to our database. 

As time goes on, eventually we're unable to see the forest for the trees, the implementation keeps getting in the way of understand the process and we're no longer able to move forward. 

We have no shared model
If the business owner and the developers don't speak the same language, and the code doesn't clearly express the business model, then how can we build software that solves a business problem? 

The answer is, we can't. Overtime, things will get worse and worse, the code will "rot" and the developers and business owners will drift apart, in both langauge and perspective. Eventually development will get insanely expensive and you'll dream of a time when you had the momentum of those early days.

What problems does this cause?

- 

Solutions don't match the process

Solutions are hard to evolve
This is a natural results of the above points. If the solutions o

This causes massive problems. First off, the business owners and the developers are speaking different langauges. This is a mjor barrier to getting across the needs of the business. If a developer is immediately translation your problem into technology, then some details are going to slip through the cracks.

We as developers will use our understanding of the system when talking about it, and since the 
The goal of DDD is to bring the language of the problem to the forefront, rather than the language of the implementation. Once the developers are speaking about the problem from the perspective of the business, then they'll be able to effectively communicate with the business, and from that build better solutions.
