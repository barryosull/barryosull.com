---
title: Domain Driven Design for Everyone Else
published: true
description: Domain Driven Design (DDD) explained in a non-technical way
tags: DDD, productivity
cover_image: http://barryosull.com/images/e9e66540-b0f1-4b5d-a1e2-d3c7c1ae0e0f.jpg
---
I've been talking a lot about Domain Driven Design (DDD) lately, be it at meetups or with clients, so I thought I'd write down my thoughts and see if it helps.

Now, lots of people have written about DDD from a technical perspective (see the end for links), so I'm not going to do that, instead I'm going to discuss DDD from a non-technical perspective. 

This is DDD for everyone else.

## Solutions Always Overrun
Designing and building a solution is not a trivial problem. It never goes smoothly, and even if it's completed on time (which is never) the solution is usually ineffective and needs to be changed, often drastically. This leads to more delays, bigger budgets and even larger problems down the line.

Why does this keep happening?

## Complex vs Complicated
Building a profitable business is a complex problem, and complex is different to complicated. 

For example, "taxes" are complicated. There are many, many interweaving rules and processes, but once you know how to apply them (the process), you've solved the problem. It becomes procedural; no thought is required. Just follow the process and you'll be fine.

Building a business is not like that at all, there are just too many unknowns. In other words, it's complex. This is fairly obvious if you think about it. If such processes existed then everyone would just use them. There would be zero risks whatsoever (and this article wouldn't need to exist). 

Complex problems cannot be controlled, [they can only be managed](https://sloanreview.mit.edu/article/the-critical-difference-between-complex-and-complicated/). Yet we still try to treat business development as a process, because we __really__ want it to be one. Just look at the popularity of "Agile"* and "Lean", especially the heavily marketed, process orientated versions, they re-enforce this illusion (and they also don't work). It's wishful thinking.

![Complicated vs Complex](http://barryosull.com/images/db8ae1ef-bea3-469f-b5be-d098d15f7636.jpg)

DDD acknowledges this fact, and instead of focussing on rigid processes and hard rules (i.e. one size fits all solutions), it presents techniques to manage and remove ambiguity. The secret isn't following a process, it's about iterating on the problem you're trying to solve.

## Focus on the (right) problem
In DDD the domain, i.e. the problem and its resulting knowledge/activities, is the driver of everything else. All solutions flow from problems, so putting the core domain at the centre will naturally lead to better solutions.

For example, say your business is online news. Your "core" domain (the one that drives your business) is content generation, everything else is secondary. By producing better content faster, you'll grow your business. However, if you focus on solving the problem of resizing images (and hire a bunch of developers to write the software) then you're probably not going to grow your business.

DDD brings clarity, and through clarity, we're able to focus.

## Talk to the experts
If you want to understand a problem, then you need to talk to the domain experts. A domain expert is someone that understands the problem better than anyone else, and they are able to tell you what's important and what isn't. 

In most organisations, the domain experts are not the ones building a solution. DDD helps to bridge the knowledge gap between what the domain experts know and what those building the solution are trying to understand.

This is why a large chunk of DDD techniques have nothing to do with technology, instead they focus on people and ways to unearth complexity and ambiguity. If we're all on the same page, it's easier to move forward.

## Building a shared understanding
One key way to gain clarity is to build a shared understanding of the problem, i.e. a domain model. If everyone has the same model in their head, then there's no ambiguity. It helps us avoid overly complicating our solutions, or worse, building the wrong one (e.g. the image resizer above).

## Sprechen Sie Talk, huh?
Language is core to DDD. We use language to express our ideas, to explore problems and define solutions. If the domain is complex, then that language will be rich and complex, with its own subtleties and nuance.

The thing is, most people in your business may not share that language. Instead they use their own language to solve problems, which is fine. However, problems arise when two or more people try to communicate using different language without realising it, e.g. the same words but with different meanings. This causes ambiguity and its a leading cause of errors and misunderstandings. 

The more people you add to the mix, the worse the problem gets. Take the typical chain of command for example. 

![The Chain of Mis-communication](http://barryosull.com/images/97309d57-0197-4a03-809a-f845a23aa60e.jpg)

That's a lot of room for mis-communication. If you've ever requested a feature and gotten back something that is way off the mark, this is why.

The solution is for everyone to work closely with domain experts to understand the language as the business defines it, and figure out where these language boundaries exist, i.e. when a word is being using in a different context. This enables everyone to communicate (i.e. have shared domain model), leading to less siloing, better collaboration and simpler solutions. 

## Your solution reflects your understanding of the problem
Any solution you build is a direct reflection of how well you understand the problem. If the solution is good, then you understand your domain, if it's bad, then you don't. This is why quick iteration is so important, it's about tightening feedback loops and iterating on the problem, not building the right solution the first time you try (which never happens).

By using your solution as feedback, you can have further discussions and figure out if you were close or not, which leads to a better domain model, which results in a new solution, which feeds back into your understanding of the problem. It's cyclic and it encourages us to better understand what we're doing, rather than focussing on the solution as the be all and end all.

## Faster feedback is better feedback
The faster you can test a solution the better. This doesn't mean you need to build software, that's the most expensive way to test. Instead why not prototype a solution with mockups or even pen and paper? You'll get the same feedback at a fraction of the timescale. DDD encourages upfront discovery and iteration, not writing software for the sake of it.

![Feedback loop](http://barryosull.com/images/0de788f4-23bc-410d-92b8-90b44d1fcd67.jpg)

That's not to say DDD doesn't talk about software a lot, it definitely does, and it has lots of patterns for writing it, but software isn't the core. A common adage in DDD is that the best software is no software. You see, software adds complexity, so if you can avoid that complexity, you should. A good DDD practitioner will try to find existing solutions to problems, only falling back on custom software if it brings the most value.

## Self Improvement
DDD is about the problem of understanding and iterating on problems. This is cyclic, meaning DDD is frequently used to understand itself and improve on itself. You can imagine how fast this feedback loop is, and DDD practitioners are always iterating, discovering and naming patterns and techniques. It only gets better with time.

## Conclusion
In short, DDD puts the business and its domain in the driving seat, where it should be. I've been applying DDD for 5 years now and I have to say that my ability to build useful solutions has increased massively. It's helped me hone the techniques and skills I need to understand and iterate on problems. If you want to get better at building solutions, I'd recommend DDD wholeheartedly.

## Further reading
- [http://learningforsustainability.net/post/complicated-complex/](http://learningforsustainability.net/post/complicated-complex/)
- [https://airbrake.io/blog/software-design/domain-driven-design](https://airbrake.io/blog/software-design/domain-driven-design)
- [https://en.wikipedia.org/wiki/Domain-driven_design](https://en.wikipedia.org/wiki/Domain-driven_design)
- [https://techbeacon.com/get-your-feet-wet-domain-driven-design-3-guiding-principles](https://techbeacon.com/get-your-feet-wet-domain-driven-design-3-guiding-principles)
- [http://www.amazon.com/exec/obidos/ASIN/0321125215/domainlanguag-20](http://www.amazon.com/exec/obidos/ASIN/0321125215/domainlanguag-20)


#### Asides:
*This is is a dig at capital A "Agile", the singular methodology that is sold by consultants as a fix all your development woes, as opposed to actual "agile". which is great, as it focuses on iterating on the problem of writing software, adapting to changes.