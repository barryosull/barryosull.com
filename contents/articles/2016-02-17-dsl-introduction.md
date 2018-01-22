---
title: Write DSLs and Code Faster
published: true
description: An introduction to DSLs and how powerful they can be.
tags: DSL, parsing, custom language 
---

# What is a DSL
DSL stands for Domain Specific language, this means that it is a language that is designed to solve a specific problem in a domain.
DSLs are great if you want to write a generic solution to a specific problem without all the boilerplate code.
A good DSL is easy to write and understand. Once someone understands the domain language, they can read the DSL, and understand the problem, even if they're not a coder.
DSLs allow us to automate many things, including aspects of development, greatly increasing our speed and lowering codes and error rates.

# Here's one I made earlier
At work, I was getting annoyed with how it always took us so long to write request handlers that decode HTTP requests into domain objects. So in my spare time I designed a DSL for automatting this process, and then added it to our codebase. Here is a full example of one of these DSL files.

## Exampe Request DSL
```
User.ScheduleAppointment has { 
  a UserId userId 
  an Appointmentatetime appointmentDatetime
  a Location location from {
    a LocationName locationName from location
    a Latitude latitude
    a Longitude longitude
  }
}
```

That's it.
This DSL defines a request coming into a server, that is then turned into a command, which is then handled by the system.
The DSL defines the command, the command's inputs, and request parameters used to create those inputs. We're using a CQRS based system with Valueobjects and Commands, so our language is very simple, strongly defined and well structured. (If you don't know that a valueoject is, [you can find out here](https://en.wikipedia.org/wiki/Value_object))

So, in the above DSL, we have a Command "User.ScheduleAppointment" (guess what this does), which has many inputs. The first input is called "userId", it is a valueobject of type "UserId". The request parameter that's passed into the ValueObject is implied from from the input's name, which becomes "userId". You can use a different request parameter by using the "from" keyword, which you can see in line 6. This allows us to handle the case that a client is sending a request that has mispelled or misnamed fields (ie, legacy clients).

In practice, sometimes a ValueObject's input is made from other ValueObjects, rather than just a request parameter (string). That's why we have the tabbed tiers of inputs surrounded by parentheses. This allows us to build very complex, tiered objects in a simple, automated manner. From this, we can easily build the tree of inputs required to create a command.

And that's it, we've defined our language. 

# So what are the advantages of this DSL?
You can already see the main advantage of this DSL, it allow us to autoamte a complex, error prone process. It turns out though, that we can use this DSL to solve various other issues, further highlighting how useful they are. Here are a few examples.

## 1. Generating requests
Making a request object client side is no longer a pain. We can use the same DSL to define the shape of the request that should be sent, and you can validate the request before you send it. Not only that, but you can automate the generation of the request itself. We created a request factory that takes in a form and tries to extract the request parameters out by key. Then we can set any remaining parameters manually. Very handy.

## 2. Sending and receiving requests
Sending requests to a server is easy, but tiring to implement, especially if the URLs are not uniformly named and structed. You end up writing a lot of boilerplate code that is painful to change. Well, you can automate this. We created a single Command endpoint that takes in the above request, and then decodes the command and runs it. Now we have only one endpoint to hit, so our code is drastially reduced, both client and server side.

## 3. Documenting requests
We can use the above DSL to create a request schema. That schema can be used in documentation, or you can make the endpoint self documenting. If someone sends a request with the method "OPTIONS", you return the Schema as JSON, making it easier for people to use and understand your API.

## 4. Speed of development
Once you understand the language, changing or writing a new request is quick and painless. With this DSL, I was able to write and validate an entire collection of requests (20 in total) in under 30 minutes. This would have taken me at least a day if I didn't have the DSL handy. 

## 5. Modular
This DSL is not just specific to our current app, it can used in any app, as long as it uses commands and valueobjects (which all our apps do/will, we're DDD/CQRS junkies). So now have another tool that can rapidly speed up development across all our applications.

# DSLs can be used anywhere
What you saw above is one simple DSL that has drastically improved our development speeds. It's automated a painful, error-prone process and it's also offered a host of other benefits. Think about that, that's just one DSL. Imagine what you could do with multiples ones, all working together! You can write DSLs to automated practically any process that can be defined in language. Which, if you think about it, is every process, as we define and comminicate processes to others (and ourselves) via language. The sky's the limit.

So to close, I say this. Start writing DSLs. You'll do what developers do best, automate processes. Learn how to do it, and you won't look back, infact, you'll wonder how you worked without it.

If you want to look at parsing the DSL in Javascript, stay tuned for my next article.
