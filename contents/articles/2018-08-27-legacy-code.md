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


