---
title: Notes from "Growing Object Oriented Software, Guided by Tests"
published: true
description: My collection of notes from an excellent book
tags: tdd, book
cover_image: https://thepracticaldev.s3.amazonaws.com/i/u5vhdlmp8u9ssqsdzwt4.jpg
---

Below is a collection of notes I made after reading [Growing Object Oriented Software, Guided by Tests](http://www.growing-object-oriented-software.com/). I highly recommend that developers read this book. Writing tests is hard, and using tests to write good code is even harder. It takes a lot of time and a lot of getting it wrong before you can get it right. 

Well, the book above explains how to do it step by step, I'm definitely a better developer for having read it. Many thanks to the code wizard that lent it to me, you know who you are. 

I'm posting these notes online because it's a useful reference for myself, and hopefully others. I've tried to group advice together to make it easier to read (for both you and me). 

### Three types of test:
- Acceptance: End to end
- Integration: Test that our code can interact with code we don't control, eg. an external API or a library
- Unit: Test our objects do the right thing (and are they easy to work with)

### Starting a new project
Always start with iteration zero, the walking skeleton. Get a single feature working end to end using real code, libraries, datastores, services, etc.. Proves that your concept works, that it is buildable, testable, deployable and it highlights integration pain points early.

### Adding features:
Start with a failing acceptance test, then enter the TDD loop until the acceptance test passes. Repeat.

Start your acceptance tests with the simplest success case. Keep a note of future failure cases, refactorings and other tasks, allows you to stay focussed on getting the simplest acceptance test passing

Write "a" failing test, don't write all test cases at once (applies to all test types).

### Testing objects
Hard to test objects most likely have a poor design. It means they are hard to understand and hard to use.

### Uncover uncertainty
Always have end to end tests, they expose uncertainty early.

### Brownfield projects (legacy codebases)
- Automate build and deploy
- Add end to end tests for the parts you want to change
- Slowly build test coverage as you update/fix the codebase (3 types where appropriate) 

### The TDD loop
1. Write a failing unit test
2. Make the test pass
3. Refactor
4. Repeat.

### A better TDD loop
1. Write a failing unit test
2. Report errors clearly
3. Make the test pass
4. Refactor
5. Repeat.

### Reporting errors
Tests should fail informatively. If a failing test doesn't help you figure out what's wrong, then it's only half helping you. 

### OOP practices
Objects communicate via messages. Messages/Values should be immutable, Objects can have state.

"Encapsulate data" is another way of saying "hide data". If an object is "hiding data" it better have a good reason. Eg. constants in the wrong class

Don't use strings, use domain types instead (ValueObjects).

### Writing tests

Put your tests in a different package to your code, this ensures you're testing the external API. 

Test behaviour, not methods. This means writing tests that show what your object is for, not what it's methods do. We need to know how to use the object to achieve a goal, not how to use the object method in isolation. Test names should describe features, not methods.

In tests, use `null` when an argument doesn't matter. Make it a named constant though, so the code is easier to read.

Keep the code compiling. The time between changing the codebase (breaking it) and fixing it should be minimal. The more code you have open and in progress, the more you have to keep in your head, slowing you down.

Working is not the same as finished. Be sure to refactor your code right after writing a passing test, it's the best time to do it, you have all the context in your head.

Distinguish between test setup and behaviour assertions, keep it clear.

Setup code should be clear and expressiveness, saying what it's doing, not how it's doing it. Messy setup code makes test hard to read and can hide fragility. Focus on expressing what you want to do, not how you plan to do it

Test code should be of the same quality as the code it's testing. 

In tests, don't mock values, use real ones instead. If they're complex to build, then use one of the many object creation patterns (eg. factory, builder) and add that to your test codebase.

When writing tests, specify what should happen and no more. Try to remove code that isn't important to the expected behaviour.

Builders are great for creating objects, especially in tests. Makes code a lot easier to read and they're reusable.

Builders can be arguments to builders, make it easier to compose complex objects while keeping code clean.

Mock objects can be viewed as tracer objects, they tell you when they're not used as expected.

Test readability and resilience tend to end up coupled. If your tests are hard to read, then there's a good chance they're fragile.

If you're using a data store in integration or acceptance tests, be sure to blank the data on setup, not teardown. If there's a crash midway through a test, then teardown never gets called and your system is in an inconsistent state. Blank during setup and you'll never have this problem.

### Writing a test, what order should I do it in?
1. Write test name
2. Write the call to the target code
3. Write assertions/expectations
4. Write the setup and teardown

### Dealing with bloated constructors: Three options
1. Package the dependencies into a new concept (wrap argument in an object)
2. Break up the class into multiple classes
3. Use default values (if the majority of constructor arguments are values)

### Software architecture
Nothing shakes out a design better than trying to implement it. Up front design is nice, but it will never get it right first time.

## Helpful tips
Use `DefectException`s, these are a sub type of exception that are only thrown if the developer did something wrong. Eg. Forgot to set an environment variable. Very handy for catching these kinds of mistakes, they're usually easy to fix once you know what you're dealing with.

Don't use literals, give values meaningful names

Commands change state, queries do not. Always remember CQS (and CQRS).

And that's it, if the above seems useful or rings true, then I'd recommend you read the full book, as there are just my notes and they don't do the book justice. Seriously, go read it, shoo.


