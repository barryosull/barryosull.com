---
title: "Folder Structure and Frameworks: What is exerting control?"
published: true
description: How you structure your code within a framework affects how you think about your system, what problems can this cause and can we solve them?
tags: ddd, architecture, software-design
cover_image: /images/frameworks-control.jpg
---
Recently I've been thinking about folder structures, specifically how we structure our web apps to encourage the design we want and to enable other developers to explore and understand the codebase. This train of thought was spurred by a problem we faced with one of our app, which I'll get into shortly.

## Folders Give Context
When we open up an application the folder structure is the first thing we see, even before we glance down at the readme. It conveys the hierarchy of concepts and hopefully how they relate to each other. A haphazard folder hurts more than it helps, especially if you have to hop around from folder to folder to figure out what anything does. Choosing the right structure is important, it's why so many frameworks come with a structure already defined, it's a foundation you can easily build on.

Now some developers advocate putting all your code into a single folder that's "per feature", e.g. controllers, DB accessors, repos, configs, etc... but honestly I've never seen this work out. It's always a jumbled mess that falls apart once you have more than 7 classes, so I'm just gonna disregard that notion straight off the bat.

With that out of the way let's look at the folder structure that was causing us issues.

## The Status Quo

```
/app
    /Funding
    	/App
	    /Domain
	    /Infra
/bootstrap
/config
/database
/public
/resources
/routes
/storage
/tests
/vendor
```

This is the structure of our web app, as you can see it's a fairly standard Laravel app, the only thing that's different is the internals of the `app` folder structure.

Quick bit of background, we structure our codebases using a [Clean Architecture](https://barryosull.com/blog/cleaning-up-your-codebase-with-a-clean-architecture/)/[Onion Architecture](https://www.codeguru.com/csharp/csharp/cs_misc/designtechniques/understanding-onion-architecture.html). I won't go into too much detail but here's a quick overview:

**Domain:** The core code of your system, models the problem you're solving. Contains no technical details (e.g. no SQL or DB concepts), focuses on business language and concepts instead.

**Application:** Compose domain objects into a single business operation (e.g. CreateUser) that can pass or fail, allow domain code to interact with external systems via interfaces (e.g. a Notification service). 

**Infrastructure:** The implementation of domain, application concepts. All technical details and framework bindings live here. This is where you glue your domain/app code to technical concepts such as databases and/or libraries. HTTP controllers live here, as they are technical concepts that plug into app/domain code.

The main reason for this is to decouple your system from implementation details, so that it's easier to design, understand and test.

## The Problem

The issue that sparked introspective into folder structure was the high level folder `app`. `app` is the default folder created by Laravel for your applications code (thus the name). However you can see that within `app` is another folder called `App`. We didn't like this as it meant there were two folders in a hierarchy with the same name despite the fact that they serve different purposes. One is the framework's concept of an `app`, the other is the defined interface for our `App`, i.e. the input and outputs decoupled from the framework and technical details. This is potentially confusing.

We had a discussion about changing the folder name to be clearer, since `app` isn't great. We iterated on a couple of names, including `components`, `src`, even `code` (I'm not joking). None of them really fit. We realised that changing the name would break Laravel convention which would confuse new developers. 

This got me thinking, why are we letting the framework control this? It's an implementation detail, yet it's exerting control over the folder structure and our thinking. On top of that our business code is now wrapped and contained by the framework code. This implies that our code is a subset of the system, even though the opposite is true. This affects our thinking and constantly re-enforces the idea that the framework is in control (even though it really isn't).

This is a problem that I've seen developers spend an awful lot of time on, they focus on how they're business code fits into the framework rather than the opposite way around. This leads to every concepts being seen through the lens of the framework, muddying the concept and making it harder for people to understand intent.

If you think about it, this structure breaks the [Dependency Inversion Principle](https://stackify.com/dependency-inversion-principle/). We have abstractions (the business concepts) being controlled by the details (the framework). That's bound to cause issues.

Instead I'd like a structure that makes encourages the separation between the code we write to solve a business problem and the code we use to do that job. 

So with all the above in mind, how would we structure our codebase? (You can probably guess where I'm going with this).

## The New Structure

```
/contexts
	/Funding
		/App
		/Domain
		/Fund
/framework
	/bootstrap
	/config
	/database
	/resources
	/routes
	/storage
/public
/tests
/vendor
```

First of, you'll notice that our `app` code is now called `contexts`. By naming it contexts we make it very clear that the code inside is solving a particular problem for a specific sub domain (here's some detail on the concept of [bounded contexts](https://martinfowler.com/bliki/BoundedContext.html)). As the application grows we'll add more contexts. `app` was far too generic a name, whereas `contexts` gives context straight away (I'm so sorry for that pun).

Second you'll notice that the framework code is now contained is its own folder structure, independent of the contexts. This makes it very clear that the framework is a detail, rather than the controller of the system. It is a component that our contexts use, rather than a system exerting design control on our contexts.

Tests is still outside, as the test code should be coupled to the contexts and not the framework. You're testing that your system works as expected, not that the framework works as expected. This structure encourages this thinking which I believe leads to better system design (don't couple test code directly to frameworks).

Public is at the root level as it usually contains lots of resources that are framework independent, the only thing that's framework/system specific is the code within the index page that boots the app, and that's not a good enough reason to bundle it and all the other resources (css, js, images, etc...) with the framework, it really is a separate thing.

Effectively I've inverted the dependencies to the folder structure, now the abstractions (the business code) is no longer contained within the details (the framework).

## Structure matters
How you structure your codebase will influence how you think about it, and our practice of building our application code inside out framework code and lead to problems, it's forces a way of thinking that just muddies the water, for junior and senior developers alike.

My goal with the above structure is to make it very clear that the contexts are the heart of the application, not the framework. This guides developers to focus on writing solid context code, written from the perspective of the domain rather than the implementation details. I believe this structure encourages better design and aids developers in understanding the distinction and responsibility of each folder. In short, less noise, more signal.

What about you? Have any interesting structures you'd like to share? Feel free to reach out in the comments below or message me on twitter.
