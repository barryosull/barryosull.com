---
title: "Developer deprogramming: Getting started in Event Sourcing"
published: true
description: Event Sourcing mistakes, a story of making them and how to avoid them
tags: event sourcing, DDD, CRUD, mistakes
cover_image: https://thepracticaldev.s3.amazonaws.com/i/10xpi9u14p5s6i0seyzd.png
---

There are two things I wish I knew when I started building Event Sourced Apps.

1. Always talk to the domain expert before building or designing anything, no matter how "simple"
2. Always start with events, don't start with a UI, no matter how "simple" it is

When we ignore the above and just forge ahead, we invariably end up with CRUD based events. Nine times out of ten these events will not mirror the actual business processes, and instead they will get in the way and make development harder and slower.

# The problem with CRUD
[CRUD](https://en.wikipedia.org/wiki/Create,_read,_update_and_delete) stands for Create/Read/Update/Delete. Most Databases operate on a CRUD model, like MySQL or MongoDB. You Read a model from the DB, change a value, and then Update the record in the DB. This is all fairly straight forward.

CRUD is how we as developers have trained ourselves to think. As part of our day to day, we translate specs into tables, then model all our operations on that data as CRUD. Hell, most of our UIs are expressed as CRUD based forms, so it's no wonder we're obsessed with the concept.  

The thing is, the domain experts don't speak in CRUD terms, that's our language, not theirs. Whenever we build a solution without talking to a domain expert we fill the knowledge gaps ourselves, using the language we know best. We end up modelling our events as CRUD operations, even if it in no way matches the language or the processes of the business itself. It turns out this causes massive headaches further down the line, as the following story will showcase.
 
# An example of getting it wrong

This story is drawn from past experience, some of the details are changed, but the core message is the same. 

We were a small team building an online training product. We had multiple independent services, one of which had a concept called a `Course`.

```yaml
Course
    title: Trust Building Tutorial
    image: https://i.ytimg.com/vi/WbVhL5LLfBA/maxresdefault.jpg
    content: https://www.youtube.com/watch?v=dQw4w9WgXcQ

```

A `Course` is a training video on a certain topic. Conceptually it's a  pretty simple object, it has a title, an image and content. These properties were simple as well, `title` is a non-blank string, and both `image` and `content` are URLs to files uploaded through the GUI. So we decided to make `Course` an aggregate, it had a simple lifecycle and was self contained. All good.

First we started with UI mockups and broke down their expected behaviour.

![Add course form](https://thepracticaldev.s3.amazonaws.com/i/he90aojp384p261t0ju5.png)

We saw that a user can Create, Update and Delete a course (you can see the CRUD language bleeding in already). Thus we came up with the following commands and events. 

#### Commands:
- CreateCourse
- UpdateCourse
- DeleteCourse

#### Events:
- CourseCreated
- CourseUpdated
- CourseDeleted

## What's wrong with this?
This all seems pretty straight forward, but look closer and you'll see the cracks in this design.

### 1. Not enough context
First off, it doesn't tell us anything interesting. A course was updated, sure, but what was actually updated? Did the title change, did the image? Any service that cares about the title changing has to keep track of that title historically in order to check if it changed. That's a lot of work for such a simple task.

### 2. Expensive to change
The above events are brittle. If the "Course" changes shape, eg. a description is added, then you have to upgrade every "CourseCreated" and "CourseUpdated" events and update every single service that consumes that event. That's a lot of extra work just to add a description to a course.

### 3. Inconsistent language
When we listened to the domain experts, we noticed that not a single person said that they had "Created a course". Instead they said they'd "Added a course". It's a subtle change, but it's clear sign that we had made up our own language, instead of using theirs. Now the dev and business teams would use different language to describe the same things, which leads to confusion and unnecessary arguments.*

## Why did this happen?
We, like most developers getting started in Event Sourcing, we were still thinking in terms of CRUD. Based on the UI, we knew the type of commands and the data they needed, so we thought we had enough detail to start implementing. When it came time to actually create the events, we used our commands as a guide, converting them to past tense and calling those the events. A one to one mapping that we had completely made up without talking to a domain expert.

In short, we started with a UI, when we should have started with domain events. No wonder it was wrong. Oops.

# How should the events actually look?

Well in order to answer this, we had to actually talk to a domain expert and figure out what they thought was important. While doing this, we needed to disregard what we had already modelled, as it was based on our own language, not the businesses. 

So we interviewed them and asked "What do you do when administrating a course?", what's important to them. Then we flipped the question and asked them "What's important to others?", who do they tell about certain types of changes?

This was an eye opening exercise. The expert told us that whenever a course was added or changed, they told the marketing and transcoding teams. Delving deeper, we asked what they actually said to those teams, and when. 

It turns out that the marketing team needed to know the current title and image for the course, or if the course was removed, they didn't care about anything else.

Conversely, the transcoding team cared about the content and nothing else.

Also, in their language, there was no distinction between creating a title/image/content and updating it, we had made that distinction up ourselves (based on our own internalisation of CRUD concepts).

This lead to the following events and commands.

#### Events:
- CourseAdded
- CourseTitleSet
- CourseImageSet
- CourseContentSet
- CourseRemoved

#### Commands:
- AddCourse
- ChangeCourse
- RemoveCourse

## Why is this better?
Now let's see how these events stack up compared to the old ones.

### 1. Lots of context
With the new events the marketing and transcoding teams/services just have to listen for the events they care about. They no longer have to sift through the events to figure out if the change is important to them, making the overall system simpler. This is an event driven example of the [Tell, don't ask principle](https://martinfowler.com/bliki/TellDontAsk.html).

### 2. Easy to change
Taking the above hypothetical situation, we want to add a description to the course. Instead of updating the existing events, we'll model this event as a new, discreet business change. So we'll simply add a new event called `CourseDescriptionSet`. This change is relatively easy to make and it only affects the aggregate, no event upgraders needed. The change doesn't affect any of the external services either, they can just keep consuming the existing `Course` events. Thus our system is less brittle to change. This is actually the [Open/Closed Principle](https://en.wikipedia.org/wiki/Open/closed_principle) in action.

### 3. Consistent language
The developer and the domain expert are speaking the same language, at the same granularity. This shared understanding is explicitly modelled in the code, making it easier to understand. This will make future interactions simpler and minimise miscommunication.

# Conclusion
After a little bit of exploration, we managed to make the `Course` aggregate much more useful to our system as a whole, exposing richer functionality than the basic CRUD implementation. 

In short, talk to the domain expert. It doesn't matter if you're building an event sourced app or not (though it really helps), talk to them. This will will let you move past basic CRUD implementations, so you can build useful, understandable software. Remember, a well fleshed out UI with sequence diagrams is no substitute for a real conversation with an expert. So don't assume you understand the problem, stay humble and ask questions, it will save you an incredible amount of time further down the line.

## An aside on talking to Domain Experts
It's important to note that we had to delve deeper into the domain expert's answers to really understand what events were important. People intuitively condense processes when explaining them to others, sometimes omitting information to keep it simple. This is why you need to explore their answers in detail to extract hidden domain concepts. Basically, keep interrogating them until they give consistent answers (do it nicely though). 

If you'd like to hear more about interviewing domain experts, please leave a comment, always happy to talk. If enough people are interested, I'll write an article about it (crowd sourced articles!).