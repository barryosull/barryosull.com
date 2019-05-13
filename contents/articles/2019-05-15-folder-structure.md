---
title: Folder Structure
published: false
description: description
tags: legacy
cover_image: http://globalnerdy.com/wordpress/wp-content/uploads/2008/07/technical_difficulties_please_stand_by.jpg
---
Blogging on folder structures

Recently I've been thinking about folder structures, specifically how we could structure our web apps to encourage the design we want and to convery useful information. This train of thought was spurred by a problem we faced with one of our app. 

First off, let's look at the structure we were deailing with (within a standard Laravel Application):

```
/app
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

Quick bit of background, we structure our codebases using a Clean Architecture/Onion Architecture. I won't go into too much detail (they're very similar), but here's the jist of it. 

Domain: The core code of your system that models the problem you're solving. Contains no technical details (e.g. SQL or DB access).
Application: Compose domain objects into a single business operation (e.g. CreateUser)
Infrastructure: All technical details and framework bindings live here. This is where you glue your domain/app code to the rest of your system.

The issue that sparked this line of thinking was the high level folder `app`. `app` is the default folder created by Laravel for your applications code (thus the name). We didn't like this as it meant there were two folders directly inside each other with the same name , `app`, then `App`. They serve different purposes though, one is the frameworks concept of an `app`, the other is the defined interface for our `App`, the input and outputs decoupled from the framework.

We had a discussion about changing the folder name to be clearer, since `app` isn't great. We iterated on a couple of names, discussed them, then realised changing the name would break Laravel convention and it would confuse new developers. 

This got me thinking, why are we letting the framework control this? It's an implementation detail, yet it's exerting control over the folder structure. 

Conceptually there's another problem as well, our code is now wrapped and contained by the framework. This implies that our code is a subset of the system, not it's definition. This isn't actually the case, but the folder structure implies it is the case and this affects our thinking.

So with all the above in mind, how would we structure our codebase?

If I were to restructure things, I'd move to this structure instead.

```
/contexts
	/Funding
		/App
		/Domain
		/Fund
	/Proposal
		/App
		/Domain
		/Infra
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

First of, you'll notice that our `app` code is now called `contexts`. By naming it contexts we make it very clear that the code inside is solving a particular problem for a specific sub domain. If you haven't looked into bounded contexts then I recommend that you do. The advantage of this is that it allows for mutiple contexts. We have the same issue above, but this way it's much clearer.

Second you'll notice that the framework code is now contained is its own folder structure, independant of the contexts. This makes it very clear that the framework is a detail, rather than the controller of the system. It is a component that our contexts use, rather than an system exerting control on our contexts.

Tests is still outside, but the test code should really have little to no knowledge of the framework code. 

Public is at the root level as it usually contains lost of resources that are framework independent, the only thing that's framework/system specific is the code within the index page that boots the app, and that's not a good enough reason to bundle it and all the other resources (css, js, images, etc...) with the framework, it really is a separate thing.

My goal with the above structure is to make it very clear that the contexts are the heart of the application, not the framework. This guides developers to focus on writing solid context code, written from the perspective of the domain rather than the implementation details.

Naming conventions
As as an aside, 