---
title: Notes from Working Effectively with Legacy Code
published: false
description: description
tags: tags
cover_image: http://globalnerdy.com/wordpress/wp-content/uploads/2008/07/technical_difficulties_please_stand_by.jpg
---
A refresher for me, possible inspiration to read the book for others.

For reasons to change software
- Adding a feature
- Fixing a bug
- Improving the design
- Optimising resource usage

Behaviour is the most important thing about software

It seems nearly impossible to add behaviour without also changing it to some degree.

Key refactoring point: There aren't supposed to be any functional changes, it should still behave the same way.

When adding behaviour, we have to know that the existing behaviour isn't changing. This is why changing existing software is so difficult.

To mitigate risk we have to ask three questions:
- What changes do we need to make?
- How will we know when we've done them correctly?
- How will we know if we haven't broken anything?

(the answer is tests :P)

There are two ways to make changes to a system:
- Edit and Pray
- Cover and Modify

Unit tests should be fast. It is not a unit test if it:
- Talks to a DB
- Communicates across a network
- Touches the file system
- You need to change the environment 

Anything that touches the above should be implemented behind an interface, called integration tests, then you mock/fake those interfaces in your unit tests. Simple.

The Legacy Code Catch-22
When we change code, we should have tests to make sure it's a safe change. To have tests, we usually have to change code.

Legacy Code Change Algorithm:
- Identify change points
- Find test points
- Break dependencies
- Write tests
- Make changes and refactor

There are two reasons to break dependencies:
- Sending: Break deps to sense when we can't access values (we can't sense the effects of calls to methods)
- Separation: Break deps when we can't cover it with tests (we cant run the code separately from the rest of the app)

Mocks objects are fakes that perform assertions

Programming languages do not support testing very well.

If you want to challenge your idea of what "good" design is, see how hard it is to pull a class out of the existing code.

Seams: A place where you can alter behaviour without editing that place
E.g. Overloading a method so that the base behaviouir is not called (really don't like this one, relies on extension, which is a code smell IMO)

Pre-processing seams are a bit of a hack, they decrease code clarity. (more of C/C++ concept)

Enabling pointL The point where the decision is made to use one behaviour or another

Link Seams: Include mocks/stubs for tests instead of actual class file (more of C/C++ concept)

"Tell code" is much easier to stub/mock, as you don't have to return anything, you just tell it to do something.

In particularly nasty legacy code, the best approach is to modify the code as little as possible while wrapping it in tests.

Breaking deps and writing tests can feel time consuming, but you don't know how long that work might have taken if you hadn't written the tests. It might have broken things or caused issues, causing more work in the near future.

With tests around code, nailing down functional problems is often easier.

If your a developer, code is your house, you have to live in it.

If you're adding a new feature, and it can written entirely as new code, add the new code to a fresh method. This is a sprout method.

Sprout Method: 
1. Identify where you need to make your code changes
2. Write a call for a new method at that point and comment it out
3. Determine what local vars you need and pass them in
4. Figure out if the method needs to return anything, if so, change the commented out code
5. Implement the method via TDD
6. Remove the comment to enable the method call

Sprout Class:
More complex. Used in the case where you have to make changes to a class, but the test is incredibly difficult to get under tests. It's just not worth the time investment. This means there's no way you can sprout a method.

Instead, you create another class to hold the changes and then use it from the source class.

Steps for Sprout Class:
1. Figure out where you need to change your code
2. Pretend the class exists, create it and call the method you like (spend time on the names), then comment it all out.
3. Determine what local vars you need and pass them in the constructor
4. Figure out if the class returns anything, if so, change the commented code to handle it
5. Write the class via TDD
6. Uncomment the code

Sprout class causes you to gut abstractions and move work into other classes. Sometimes things that should have stayed in one class end up in sprouts, just to make changes possible and safe.

Wrap method:
Most of the time you add code to a method, you're doing so because the code just happens to need to execute at the same time as the rest of the code. This is temporal coupling and has little to do with the methods expected behaviour.

Instead of just adding the code, wrap the old code in a new method, add the new code in it's own method, then call both from the old method.

The hardest part of this is giving a useful name to new method. Beware of giving crap names.

Wrap class: Basically the decorator pattern. Add easy to test behaviour by decorating existing classes (pretty nice actually).

Sprout method versus Wrap method: Use srout when the exiting algo is clear. Use Wrap when the new behaviour is as important as the existing.

When to use Wrap class:
- The behaviour you want to add is independent and shouldn't be in the existing class
- The class is already too large and I do not want to be party to war crimes (making it worse). Wrap it for now and move on.

These little improvements add up. After a few months things will start to look up.

As a codebase grows, it eventually surpasses understanding. It's too big to keep in your head all at once. The time is takes to figure out what change to make keeps increasing.

In well maintained systems, it still takes time to figure out how to make a change, but once you do it's usually easy and it feels comfortable. In legecy is takes longers, and you feel like you're doing something risky.

Figure out which dependencies will get in the way is easy, just attempt to use the classes in a test harness. That's highlight it very quickly.

Interfaces should change far less often than the code behind them. If that's not the case, then you've got the wrong boundaries.

The TDD Algo:
- Write a failing test
- Get it to run (e.g. no "method not found" errors)
- Get it to pass(no failures)
- Remove duplication
- Repeat

"Rename Class" is a very powerful technique. It changes how we view the code and lets us see opportunities we wouldn't have before.

Whenever possible, avoid overridng concrete methods. If you do, see if you can still call the overridden method.

Four reasons that code is hard to get into a test harness
- Instances of the object are hard to create
- The test itself is hard to build
- The constructor has bad side effects
- Loads of work happens in the constructor and we don't have access

Best way to tell a class will be hard to test is to try to test it

Test code should be clean, easy to understand and simple to change.

Don't pass null in production code unless you have no other choice (bit Java specific)

Hidden dependency, the class uses a resource that is available in the active system but not to the test.
E.g. A booted db accessor that loaded via a singleton.

The answer is simple. Create the object outside of the class and inject it. 
There are ways to make this safe without changing the signature. Easiest is to move most of the logic into a new method, have the constructor pass the object is creates, then just test the new method.

The construction blob: A constructor creates objects and uses them create other objects.
Supersede Instance Variable is a solution, but it involves creating a setter method for created objects. Avoid if you can!

Singleton is used mainly for global variables and shitty dependency injection.
If a class under test uses a singleton, then add method to the singleton that allows you to swap the actual instance for a mock.

Onion Parameter: When an object require a dep, which requires more deps, each of which require other deps ... etc.
Fake object is the obvious answer.

When you extract an interface, you are brutally severing the connection to the class. (like this wording).

Method testing, reasons it can be difficult
- Method is not accessible (private)
- The param are hard to create
- The method has bas side effects (modifys the DB)
- We need to sense through an object the method uses

For private methods, sometimes you can test them through a public method, provided you can sense the effects.

You can also move the private method to its own class. Easier to test there.

Design that isn't testable is bad.

Sometimes objects are hard to test because they have too many dependencies.

If a method is protected, you can subclass it to get access (also a shitty technique)

Don't use reflection to test private methods. It's hacky and incredibly brittle. Just bite the bullet and extract a class.

Use finally sparingly (DISAGREE!)

Sometimes class don't return anything, the side effects are hidden.

CQS is useful here. Add a query method that let's you ask questions.

Refactoring tools are your friend, as they can refactor code safely (need to learn how they work in PHPStorm)

Characterisation tests: Pin down the behaviour that's already there

If an object is messy, create a map of the object it calls and changes.

When a program is poorly written, sometimes it hard to understand why the results are what they are.

When sketching effects for a class, make sure you have found all the clients of the class, even super or sub classes.

An effect sketch can help us see where we can sense different kinds of changes.

Watch out for sneaky effects, like an object changing the state of dependencies (e.g. an object chaging and array passed by ref)

Effects propogate in three ways
- Return values are used by a caller
- Modification of params that used elsewhere later on
- Modification of static or global data

Restrict effects if you can

When you have to make a choice between encapsulation or test coverage, opt for test coverages, Black boxes help no one. Encapsulation is a tool for easing understanding, not an end goal.

Try to test one level back, i.e. find a place where you can write tests for several changes at once.

Higher level tests (acceptance/integration) are important, but they are not a substitude for unit tests, merely a step towards them.

Interception points: Areas in code where you can detect the effects of change

Find where you need to make a change, then flow outward, anywhere you can detect a change is an interception point (though the first may not be the best one)
In practice it's better to pick points closer to your interception points

Don't let unit tests grow into mini-integration tests.

Tests are a mechanism to help us find bugs later.

Good tests exercise a specific path and ensure conversion along that path work correctly.

If you use libraries, try to hide them behind interfaces. It'll slow you down a bit now, but future you will be so thankful (also makes testing easier).

If a piece of code is incredibly messy and hard to understand, try to write a short paragraph describing what it does.







