---
title: Why I don't like traits
published: true
description: Avoid using Traits in PHP, they lead to bad code 
tags: php, software-design
cover_image: https://thepracticaldev.s3.amazonaws.com/i/mdufkfeb46p3xtxmtpjv.jpg
---

Traits in PHP are a bit shite. At best they are an ineffective way to append functionality to a class, at worst they are an anti-pattern. They are often used as toggles for internal functionality (see Laravel's Acceptance tests) or as a lazy way to share common functions across a bunch of classes without using another pattern.

# Why are they shite?

## Reading:
They're hard to read. If you see that a class uses a trait, you have to open the trait to see what it's adding to the class. 

Usually the trait uses protected properties of the class, this means you have to flip between the trait and the parent class to figure out the type of the property and what it's used for. 

It can go the other direction as well, you can use traits to add or override protected methods on a class, then call them from that class. Now you have two way coupling, not one way, which makes things even more confusing.

## Extending:
Another issue with using protected properties/methods in traits. You've coupled the internals of your class to another object (the trait) that is most likely used elsewhere. This means that the internals of one class is bound to the internals of another, if one changes, the other must change. 

Coupling issue like this are inevitable, but other patterns make this coupling explicit (eg. factory/strategy patterns), whereas in traits it is implicit. 

In short, it encourages a lazy design that exposes and relies on transient internals, breaking the concept of encapsulation.

## Can't test in isolation:
A big bug bear of mine, normally you can't test traits in isolation. If you want to test the trait in isolation, you have to create a class that uses it. 

This by definition is not isolation. If the trait is only ever used for adding protected methods, then you really have no way to ensure it behaves as expected (you should use the factory/strategy pattern instead of traits in this case).

## Not using private or protected properties:
The above assumes you're adding behaviour by either accessing protected properties or adding protected methods. What if you're doing neither? 

In that case you're only using the public interface of the object, which means you can get the same behaviour by creating a function/object and passing the object as a parameter. Nice and simple, more expressive and easier to understand.

# How to work around it?
Well, if you're using traits with protected properties/methods, it's clear that there's another intermediate object that hasn't been expressed yet. I'll give an example.

We were using a trait to add two protected methods to two classes. This means that both of these classes referenced two protected methods that they didn't implement, the trait did. 

The trait functions also used a protected property (a service) of the parent classes to do their job. I wasn't happy with this, as it made the code harder to read and it forced a tight two way coupling between the trait and all it's users.

So instead we turned each protected function into its own class taking in the service as a constructor parameter. The protected functions didn't change state, they merely created an object, so the classes became factories. 

These factory classes were then injected into the constructor of each object and used internally, their service dependency was injected automatically in the process. The class then used these objects everywhere the original protected methods were used. 

The result was pretty SOLID (pun intended), we had code that was easier to read, to extend and to test in isolation.

So, in conclusion, traits break the principles of OOP, they couple internals across objects and just make it harder to understand what the hell is going on and to change your code. 

**TL;DR** : Traits are shite, use other patterns instead.

