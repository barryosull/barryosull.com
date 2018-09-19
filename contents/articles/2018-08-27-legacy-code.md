---
title: Notes from Working Effectively with Legacy Code
published: false
description: description
tags: tags
cover_image: http://globalnerdy.com/wordpress/wp-content/uploads/2008/07/technical_difficulties_please_stand_by.jpg
---
These are notes from reading "Working Effectively with Legacy Code", with my own unique spin and interpretations based on working with Legacy code day to day. If the below is interesting to you, then I highly advice you buy the book and practice it's techniques.

Below are my notes on things, you know, the things that I do when writing code. I test and extract stuff.

Now, onto the notes.

## Four reasons to change software
When it comes down to it there are four abstract reasons to change code. You are ...
- Adding a feature
- Fixing a bug
- Improving the design (make it easier to understand, express intent)
- Optimising resource usage (fewer, more efficient DB queries)

How you change and test your code depends entirely on which of the above you're doing. Always keep the context of your reason in mind and don't try to change code for multiple reasons. I.e. trying to add a feature and improve the design at the same time is going to result in a feature that kinda works with code that is sorta readable. Keep it simple, focus on what you're doing. You can swtich to another reason/mindset later.

## Behaviour and change
Behaviour is the most important thing about software. If we can't get the behaviour we want, then the software has failed. Bad code gets in the way of changing behaviour, that's what makes it bad. Conversely, you could have a horribly messy function, but if it never needs to be changed then it's not bad code. Again, it's all about intent.

It's nearly impossible to add behaviour without also changing it to some degree. If you add a new `phone_number` field to a user, you need to change the user concept and it's storage system. There's just no escaping it.

When we're adding behaviour, we also need to ensure we haven't broken existing behaviour. If a user can now have phone number but they can't login, then we're in trouble. This is the key reason that makes changing existing software so difficult. (It's also the main reason that we write tests)

## Mitigating Risk

To mitigate risk when changing code we have to ask three questions:
1. What changes are we going to make?
2. How will we know we've made them correctly?
3. How will we know we haven't broken anything?

_Spoiler: The answer to all three is "tests"._

There are two ways to make changes to a system:
1. Edit and Pray
2. Cover and Modify

One of these is the statues quo, the other actually works. Figuring out which is left as an exercise to the reader.

Ok, no it's not. The second one is the good one.

"Cover and Modify" is a strategy where we cover the code we need to change in a test, then make the changes. It's the only stable way to make changes to software (excluding automatic refactoring), otherwise you really are just hoping, and hope is not a strategy.

#### The Legacy Code Catch-22
> When we change code, we should have tests to make sure it's a safe change. To have tests, we usually have to change code.

If you want to safely change legacy code, then this is the high level process:
1. Identify change points
2. Find test points
3. Break dependencies (if you need to)
4. Write tests
5. Make changes and refactor

When do you break dependencies? Well there are two reasons:

#### 1. Sensing: 
Break dependencies to sense when we can't access values. For example, we can't sense the effects of calls to s particular method, but we break the dependencu and use a mock to check that the method was called. 

#### 2. Separation: 
Break dependencies when we can't put the code under tests. This happens when a particular object/dependency is tricky to instantiate or has methods that rely on lots of other class (think of a God class that acesses every table). It makes it practically impossible to test the code separately from the rest of the application.

A solid technique for altering behaviour is to find a seam.
A "seam" is a place where you can alter behaviour without editing that place. Think of appending functionality to the base class, or overloading a method so that the base behaviour is not called.

Every seam has an enabaling points, these are points where the decision is made to use one behaviour over another. These points are useful for switching to the behaviour you want. Sometimes these are conditional statements, or factories, or even bindings in the Dependency Injection system.

In particularly nasty legacy code, the best approach is to modify the code as little as possible while wrapping it in tests.

### Writing Unit tests
Unit tests should be fast. These are the main culprits for slow unit tests and they should be avoided:
- Talks to a DB
- Communicates across a network
- Touches the file system
- You need to change the environment 

Originally I agreed with all of the above, but since watching [this talk](https://youtu.be/EZ05e7EMOLM) I've come to reconsider it. The reason for these "rules" has nothing to do with purity, and everything to do with practicality. Each of the above prevent you running your tests in isolation (as they're accessing shared resources) and they'll be slower to run.
This used to be the case, but with the advances in DBs, FileSystem and Networks, most of the time the costs are easy to swallow. 

You should only replace them with the mocks if they start to interfere with each or they get too slow (>100ms), that's it.

If you want to turn one of the above unit test into a pure unit test though (probably because it's slow), do the following: 
- Move all code that touches the problematic resource into private methods (give them good names if you can)
- Remove dependencies on internal properties not related to that resource, pass them as arugments instead
- Create an interface with methods names after those private methods
- Extract the logic into a concrete instance of that interface
- Make that interface a dependency, inject the above concrete instance into it on creation
- Mock that class/interface in your test and now it's a real unit test
- Optional: Write a contract test for that class/interface

### Mocking
Method calls usually fall into two categories, "tell code" that changes state, or "ask code" that returns code. (I.e. [CQS](https://en.wikipedia.org/wiki/Command%E2%80%93query_separation))
"Tell code" (Command) is much easier to stub/mock, as you don't have to return anything, you just tell it to do something.
"Ask code" (Query) is harder, as you have to return something, which requires more stubbing. This is why "tell don't ask" is a programming principle, it makes writing code easier as you have less logic bleeding out of something, makes it easier to use and test.

### Tests are worth it
A lot of people don't write tests, and with good reason. It feels slow and problematic, plus we're awful at teaching each other how to do it. We're quite dogmatic when it comes to testing ("dogmatic developers?!" I hear you exclaim in surprise!).

Writing tests and breaking dependencies and can feel time consuming, but the truth is you don't know how long that work might have taken if you hadn't written the tests. It might have broken things or caused unintended side-effect, causing more work in the near future, usually when you're knee deep in another issue. (Happend to me recently)

> If you want to challenge your idea of what "good" design is, see how hard it is to pull a class out of the existing code.

With tests around code, nailing down functional problems is often easier. If you're a developer, code is your house, you have to live in it.

### Technique: Sprout Method 
If you're adding a new feature, and it can written entirely as new code, add the new code to a fresh method. This is a sprout method.

Steps:
1. Identify where you need to make your code changes
2. Write a call for a new method at that point and comment it out
3. Determine what local vars you need and pass them in
4. Figure out if the method needs to return anything, if so, change the commented out code
5. Implement the method via TDD
6. Remove the comment to enable the method call

### Technique: Sprout Class 
A more complex version. Used in the case where you have to make changes to a class, but the test is incredibly difficult to get under tests, it's just not worth the time investment. This means there's no way you can apply the "sprout method".

Instead, you create another class to hold the changes and then use it from the source class.

Steps:
1. Figure out where you need to change your code
2. Pretend the class exists, create it and call the method you want to exist (spend time on the names), then comment it all out
3. Determine what local vars you need and pass them in the constructor or method (whichever is appropriate)
4. Figure out if the class returns anything, if so, change the commented code to handle it
5. Write the class via TDD
6. Uncomment the code

Sprout class causes you to gut abstractions and move work into other classes. Sometimes things that should have stayed in one class end up in sprouts, just to make changes possible and safe. It's the nature of dealing with legacy code.

### Technique: Wrap Method
Most of the time you add code to a method, you're doing so because the code just happens to need to execute at the same time as the rest of the code. This is temporal coupling and has little to do with the methods expected behaviour.

Instead of just adding the code, wrap the old code in a method, add the new code in it's own method, then call both from the old method.

The hardest part of this is giving a useful name to new method. Beware of giving crap names.

### Technique: Wrap Class
Wrap class: Class based version of "wrap method". Basically the decorator pattern. Add easy to test behaviour by decorating existing classes (pretty nice actually).

### Choosing which to use
**When to use the Sprout method over the Wrap method:** 
Use sprout when the exiting algorithm is clear and the behaviour belongs there. Use Wrap when the new behaviour is as important as the existing.

**When to use Wrap class:**
- The behaviour you want to add is independent and shouldn't be in the existing class
- The class is already too large and I do not want to be party to war crimes (i.e. adding more code). Wrap it for now and move on is the best advice.

These little improvements add up over time. After a few months things will start to look up.

### Eventually you can't keep it all in your head
As a codebase grows, it eventually surpasses understanding. It's just too large to keep in your head all at once. As you add more and more code it takes longer and longer to figure out what change you need to make to a system. Bugs start to appear more frequently and even simple looking changes become problematic. All the while the codebase just keeps growing, compunding the problem.

In a well maintained systems, it still takes time to figure out how to make a change, but once you do it's usually easy and it feels comfortable. In legacy system it takes longer, and it saps confidence. You feel like you're doing something risky, because you are.

> Want to figure out which dependencies will get in the way of testing? Easy, just attempt to use the classes in a test harness. That'll highlight it very quickly. (I've done this many times, super useful)

Well chosen interfaces should change far less often than the code behind them. If that's not the case, then you've got the wrong boundaries. 

### The TDD Algo:
- Write a failing test
- Get it to run (e.g. no errors such as "method not found")
- Get it to pass (no failures)
- Remove duplication
- Repeat

"Rename Class" is a very powerful technique. It changes how we view the code and lets us see opportunities we wouldn't have before.

Whenever possible, avoid overriding concrete methods. It's fine for testing, since the code will never be used in production, but avoid it you can. If you can't avoid it, see if you can still call the overridden method.

### Four reasons that code is hard to get into a test harness
- Instances of the object are hard to create
- The test itself is hard to build
- The constructor has bad side effects
- Loads of work happens in the constructor and we don't have access

> Test code should be clean, easy to understand and simple to change.

Don't pass null in production code unless you have no other choice (bit Java specific).

### Hidden dependencies
This hapens when a class uses a resource that is available in the active system but not to the test. For exmaple, say the class use a `UserRepostiory` singleton that requires an active DB connection. This could get in the way of testing.

If you have this issue then the answer is simple. Create the object outside of the class and inject it in the constructor. This changes the signature, so it could cause issues for other places the object is created. If so, make the dep optional and create a local reference in the constructor if it's null. Best of both worlds.

The construction blob: A constructor creates objects and uses them to create other objects. Very hard to get under test. You can use the "Supersede Instance Variable" technique, but it involves creating a setter method for created objects. Avoid if you can!

### Dealing with singletons
Singletons are mainly used for global variables and shitty (because hidden) dependency injection.
If a class under test uses a singleton and it's making it harder to test, add q method to the singleton that allows you to swap the actual instance for a mock. It's not a great pattern and could lead to abuse, but sometimes its necessary.

Onion Parameter: When an object require a dep, which requires more deps, each of which require other deps ... etc. You get the idea. The obvious answer here is to avoid creating the object, just use a fake one instead.

When you extract an interface, you are brutally severing the connection to the class. (like this wording).

### Method testing:
Testing methods can be difficult for a variety of reasons:
- Method is not accessible (private)
- The params are hard to create
- The method has bas side effects (modifies the DB)
- We need to sense through an object the method uses

For private/protected methods, sometimes you can test them through a public method, provided you can sense the effects. You can also move the private method to its own class. Easier to test that instead.

Don't use reflection to test private methods though. It's hacky and incredibly brittle. Just bite the bullet and extract the class.

If a method is protected, you can subclass it to get access, a shitty technique but still useful when getting started.

> An untestable design is a bad design.

Sometimes class don't return anything, making the side effects hidden. CQS is useful here. Add a query method that let's you ask questions about what happened.

Refactoring tools are your friend, as they can refactor code safely

**Key refactoring point:** 
There aren't supposed to be any functional changes, it should still behave the same way. If you have to change the tests behaviour, you're doing more than refactroing (or the test knows too much about the internals of your class).

Characterisation tests: Tests that pin down the behaviour that's already there. So named because you're trying to categorise the behaviour.

### Dealing with messy objects
When a program is poorly written, sometimes its hard to understand why the results are what they are. If an object is messy, create a map of the object it calls and changes. Draw it out and map these connections. Then do scenarios, tracing different calls through the object and its dependencies. This is called effect sketching.

When "effect sektching" a class, make sure you have found all the clients of the class, even super or sub classes. An effect sketch can help us see where we can sense different kinds of changes. Watch out for sneaky effects, like an object changing the state of dependencies.

Effects propagate in three ways
1. Return values are used by a caller
2. Modification of params that used elsewhere later on
3. Modification of static or global data

Restrict these effects if you can.

When you have to make a choice between encapsulation or test coverage, opt for test coverage, black boxes help no one. Encapsulation is a tool for easing understanding, not an end goal in and of itself.

Try to test one level back, i.e. find a place where you can write tests for several changes at once.

Higher level tests (acceptance/integration) are important, but they are not a substitute for unit tests, merely a step towards them.

### Interception points: 
There are areas in code where you can detect the effects of change. Find where you need to make a change, then flow outward, look for anywhere you can detect a change. This is an interception point (though the first may not be the best one).
In practice it's better to pick points closer to your interception points.

Don't let unit tests grow into mini-integration tests.

Remember, tests are a mechanism to help us find bugs later.

Good tests exercise a specific path and ensure conversions along that path work correctly.

### Libraries
If you use libraries, try to hide them behind interfaces. It'll slow you down a bit now, but future you will be so thankful (also makes testing easier).

### Write a story
If a piece of code is incredibly messy and hard to understand, try to write a short paragraph describing what it does.

The great thing about sketching/writing about parts of a design you are trying to understand is that it's informal and infectious. 
(I do this but rarely, though it is useful, it just feels easier to power through, sketch feel it slows me down, but does it?)

### Scratch Refactoring:
 Create a branch, and just mess around trying to refactor it so you get a better understanding. When you're happy you've learned all you can, throw it away. Take half an hour and try the idea, you'll learn far more than by just looking at the code. This has worked wonders for me.

Delete unused code. It just gets in the way of clarity.

### Reason for a lack of structure
What gets in the way of architectural awareness? Couple of things actually:
- The system is so complex it takes time for someone to get the big picture
- The system is so complex there is no big picture 
- The team is in reactive mode, dealing with emergencies so much that they lose sight of any big picture

This happens when architecture is either one person's job, or no ones job. Architecture is too important to be left to just a few people. That's not to say you can't have an "architect" role, but it's important that everyone knows the architectural plan and has a stake in it. They know what to do when they need to make key decisions. It's a team effort and a large chunk of the architects job is to listen and to teach.

### Gaining understanding through stories
Tell stories of the system to each other. Simplify and condense the functionality, have a shared view. Try telling the story in different ways.
(This is really domain modelling, where the domain is the messy system, you're learning it's language, or at least creating it)

When someone describes a system, ask for the simplifications. Where did you generalise or skip over details?
Once they've finished one deep dive, ask if there's more to to tool/concept/system. Repeat until everyone is satisfied or you hit knowledge walls. Maybe pair off an do s spike into that knowledge gap. If it's a code problem, do a quick pair code dive.

> There's something about a large chunk of procedural code, it just begs for more code. Ignore it's sultry charms.

### The problem with big classes:
- Confusion: Having 50 to 60 methods makes it difficult to get a sense of it's actual use
- Many reasons to change: When a class has 20+ responsibilities, it will change A LOT
- Painful to test

### Tips for extracting objects
Extracting objects: Look for similar methods and private vars only used by those methods, group them in code, see if there's an object/concept hidden there. A class with lots of private/protected is a sure sign there's a class hiding within it.

Another tip is to look for the primary responsibility. Try to describe the class in a single sentence, if you can't then the second sentence (or more) is probably another class.

### Duplicated code:
A classic legacy issue; duplicated code. When you find you have to make the same change in multiple places to make it work.
Thankfully this is easier to solve, just remove the duplication piece by piece. Extract a class/function, test it, call it in the other locations.

An important heuristic though, start small. Remove tiny pieces of duplication, it makes the big picture clearer.

When two methods looks roughly the same, extract the differences to other methods, when you do that, you can often make them the exact same and remove one of them.

When you remove duplication, designs start to emerge naturally. No planning required, they just happen.

The ultimate goal of coding, you don't have to change a lot of code to add new behaviour.

### Monster methods: 
These are methods so large you are afraid to touch them.

There are sub types of these, with chunks of monster methods falling into the following.
- **Bulleted Method:** Methods with nearly no indentation
- **Snarled Methods:** A method dominated by a single large indented section

When dealing with these you should lean on automated refactoring at the start, use it to get code into a place that's easy to test, which makes future changes easier.

### Sense Variables: 
Sometime you'll want to refactor a method, and find it difficult to check that you haven't broken conditional logic. Enter the sensing variable. This is a variable added to a class so you can sense which conditional paths are taken. They are temporary and only exist during a refactor. Here's how to use them:
- Add the variable and give it null/false value
- In the test, check the value is the correct value
- Watch it ail
- In the conditonal path set the variable
- Watch the test pass
- Refactor the method
- Remove the variable from both conditional and test

Find code that you can extract confidently without tests, then add tests to cover them. Only do this for five lines at most, aim for three. (Seems kind of small, but ok, he might have something, you can always combine it back later)

Not all behaviours are equal in a system, some have more value. 

### Break out method object:
Extract a monster method into its own class. Surprisingly powerful technique, opens up opportunities for testing and refactoring.

### Skeletonizing: 
Extract private methods so that only the control structures (conditional statements) and behavioural calls are left.
If you can easily combine repeating control and behaviour blocks into a single method, you should, it brings clarity.

When it comes to extracting methods and creating classes, don't jump the gun.
Don't worry about extracted methods having awkward names, just stick with them, don't begin extracting a class too soon. Only after you've finishing refactoring that class should you look for classes to extract.

BTW, be prepared to redo extractions. You won't always get it right the first time, sometimes extraction needs to be reverted. This usually happens because your previous extraction made it so obvious.

> Do one thing at a time. Pair programming is great for encouraging this.

### Morale
The grass isn't really much greener in green-field development, this I can assure you.

If you want to boost morale, pick the ugliest, most obnoxious piece of code in the system and get it under test. That should give everyone a feeling of control.

### Tips:
- If several globals are always used or modified near each other, then they belong in the same class
- Class names should be good, but they don't have to be perfect
- If you have a method that doesn't use instance or methods, you can turn it into a static method, which is much easier to test
- When extracting an interface, try to come up with a new name for it if possible, or rename the base method to include more context (e.g. `UserRepository` becomes `UserRepositoryDB`)
- Avoid abstract classes if you can, they make testing harder. (Agreed)
- You can give objects setters so you can sense changes, but it makes the object brittle and encourages bad design. When you don't have setters the system is easier to understand.

> Programming languages do not support testing very well.

Aint that the truth!

Phew, that's the end of my notes, quite a beast. If you found these notes useful then I yet again encourage you to read the original book. I'm going to write more about testing, so if you're interested please check back here for more content!