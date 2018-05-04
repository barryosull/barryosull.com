---
title: ""
published: false
description: ""
tags: 
cover_image:
---

You have an existing codebase and you want to make a change to it, but the system wasn't written test first, it may not even have tests. How do you go about restructuring the system so that you can make your changes and prove they work, without breaking existing functionality?

Afterall, that's end goal, release software that works. Otherwise we're just making changes and hoping they work, which as a development strategy it's not a great one. It's short term, it guarantees poor solution stability (things will keep breaking, causing customer pain), and costly/risky development (it takes more and time and money to make changes). 

It's a difficult position to be in, to take that codebase and add your changes safely. How do you even start?

## Disclaimer
This is by no means a how-to guide for any codebase, this just how we ended up doing it. My hope is that this will give you some guidelines or ideas on how to do it in your codebase, or at least show you that it's possible and not as hard as you think.

## What we have:
We have an interaction logging system. It logs every interaction in the app, and then runs analytics on that data. That's it. The business wanted real-time numbers on how an article is doing for their advertisers and this was the solution. It's logs these interactions to a DB table, then a script periodically aggregates the data. Every night it archives historical interactions into an archive table in the same database. This system logs between 5 and 7 million interactions each day, so there's a lot of data.

The codebase was framework-less and it was written by a mid-level dev using whatever patterns he felt were appropriate. It had unit tests for most components, but it had no integration or acceptance tests.

## The changes:
There were problems with the above implementation. First off, it was failing everytime we archived the logs. The implementation just didn't factor for the sheer volume of data and it was locking a MySQL table for 5 mins each night, causing thousands of failed requests. It was also inserting duplicate entries every now and again and we didn't know why.
There was clearly an issue with the way it was storing the logs and how it we archiving the data. I.e. the implementation was getting in the way.
Our fix was to switch to a different implementation that didn't suffer from the same limitations.

## Where we want to go:
It's important to have a vision for how you'd like the code to look at the end, an idea of the shape you'd like it to have. You don't have to have everything mapped out, but you should at least have an idea of a structure would make it easier for you to work with the code. For us, clarity and separation of concerns was the target, so we aimed migrate the system over to a layered architecture, piece by piece. This would allow us to separate the business logic from the implementation, and make it easy to swap out the implementation while keeping everything stable along the away. The above is where we want to end up, this is how we want to get there.

# Refactoring
Now that we had an idea of where we wanted it to go, we began iterating on the code. Rather than doing one big re-write (which is a TERRIBLE idea), we opted to do it in small interative chunks, slowly massaging the code into it's new shape. In the end we'd be able to review each of the steps, and show the evolution of the codebase to the rest of them, showing them that refactoring and restructuring existing untested code is not as hard or as difficult as it sounds.

# 1: Setup testing
This step was simple. Setup our test code so that you can actually start up the server and prove it works. Without this step we can't write any tests, so let's prove we can bootup the app and send it requests successfully.

This step was all about getting confidence and momentum. Once we had this is in place we were much more comfortable with the viability of the refactor, we knew we could send tests to the server and it would accept them. This was our spring board for future work.

# 2: Write the Acceptance tests
Before you can even begin changing code, you must have tests in place to prove you haven't broken anything. This isn't about unit testing existing components, this about writing tests that treats your codecase as a blackbox. Data goes in (e.g. A http request), data goes out (e.g. a DB insert, file stored on S3, or an API call). You want to do this while making minimal changes to your codebase (ideally none).

This sounds tricky, but it's not as hard as you'd think. Take the logging of interactions, to test the existing feature as a blackbox, we first wrote a test that actually sends a request over http, and then checks that the interaction data is now in the DB. That's it. With that in place, we could now make changes to the internals of the codebase without fear of inadvertently breaking that feature.

I've actually written an entire article on this, have a look here if you'd like to see how it easy it is.

# 3: Start building the domain
Now that we have that code path under test, we can start looking at what the system does and separate that from how it does it. This means we try to figure out what the business operation is and make that explicit. Start small with ValueObjects

# 4: Extract http details into controllers

Step 5: Define and extract the repositoriesm
Create the repository interface, implement it and write the tests
Use the repo in acceptance tests, instead of exposing implementation details

# 6: Add the application layer

# 7: Switch to a PSR3 container

# 8: Switch to a DI with auto wiring

# 9: Add code sniffer

# 10: Write acceptance test for cleanup script

# 11: Add application layer

# 12: Move all http logic to controllers

----------

TODO:
Explain layered architecture
Show an image of the new code structure
