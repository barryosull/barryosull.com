---
title: Solution Infatuation - The con-man of Anti-patterns
published: false
description: description
tags: legacy
cover_image: http://globalnerdy.com/wordpress/wp-content/uploads/2008/07/technical_difficulties_please_stand_by.jpg
---

I'm here today to talk about an anti-pattern I've been noticing more and more, especially in companies with older codebases. This anti-pattern hinders and sometimes destroys any attempt to deal with technical debt, often leaving things worse than you found it.

I call it "Solution Infatuation".

Solution Infatuation is an anti-pattern that emerges when a team gets frustrated with the existing system. They decide that a new system/service, rewritten from the ground up, will allows them to bypass all the issues they're having with the existing system. 

The sales pitch usually looks like this: 
"Our plan is to replace the old messy code with a new lib/service that is elegant, simple and well written. It will use the latest tools/libraries, making development much smoother/faster. We can completely ignore all the old messy code and just switch over to lib/service, allowing us to bypass our existing technical debt."

Sound great right? Well, there's one fundamental issue with the above. The solution is driving the design, not the problem. By this I mean that they are designing an idealised solution, one that bypasses the existing problem space. The thing is, the current "messy" codebase, the problem they are trying to solve, is actually in control of this process. If your solution doesn't integrate into the problem space, then you're screwed.



When they're designing their new "messiah", they are letting the idealised solution drive design of the new system, rather than the problem space. The solution is driving the design of the system, not the problem. I.e. your idealised view of how the solution should work shapes it's design, not the problem that your existing system is actually trying to solve. 

I've seen this time and time again. Let's look at some real world examples from companies I've worked with.

## The Micro-service
I was called into a company to help them deal with some legacy issues. They had written a micro-service that's job was to tally up interactions, but it was tallying the numbers incorrectly, so they couldn't integrate it into the existing monolith. The bug was simple enough to fix, but that's when then we stumbled into the really tricky part, the integration.

We tried to integrate this conceptually simple service into the monolith, twice, and each time we got it wrong. The reason, we didn't understand what the existing system actually did, and any attempt to integrate it failed as we were letting our new solution shape the integration, giving it control, rather than the existing system.

The service was replacing functionality in the existing monolith, the reason they were replacing it? The current system was too messy and no one knew how it worked, so they opted to write around the problem and created a new service.

Bad move. Why was this a bad move? Well, first off they didn't understand the problem space at all, as they didn't understand the code that was currently solving the problem. This means that any solution they created was a fantasy, it wasn't grounded in the problem space, i.e. reality.

This proved to be the case when we did the integration, the microservice was missing core functionality that made it practically useless to the monolith, the "easy" integration they had planned was an impossibility.

They had become infatuated with the solution they had created, and stopped paying attention to the problem. The problem (the existing code) was in control of the situation, it defined the solution. By ignoring the problem space and instead focussing on the idealised solution space, they had doomed themselves to failure.

So how did we solve this? Well, we went back to the problem space, the legacy code, and spent time actually understanding what it was doing, since it would drive the shape of any solution we created. We used automatic refactoring tools to move logic behind a single interface (facade), then started writing tests for that interface, defining and refining its behaviour. Once we had that in place we actually understood what it was doing and what was missing from the other service. We added in new integrations for that code

In the end we had to 

## The distributed monolith
Let's give another one, another company I've consulted with. They have a legacy codebase that is actually a mishmash of four different services. Each service was meant to be independent, but they ended up coupled together and can't really function without each other. It's gotten to the stage that it's unmaintainable and they want to fix it, but don't know how.

They had gotten into this situation by following the above patten. They noticed there was a problem, they opted to write a new system that would solve this problem. They wrote this idealised system, tried to integrate it into the existing system, then realised 

## How to solve it
What do you do if you're already in the above situation?

## How to avoid it
The simplest way to avoid it is to spend time understanding the system that you're trying to replace. It's in control of the shape of any solution, so if you don't understand it, then there's no hope you'll write a useful replacement.

The best way to do this is lean on 

Each time the legacy codebase got unwieldy, th   

Each time the sa

## Next steps for the article:

I'm not really happy with it. There's an idea there, but it needs to be fleshed out further and possibly restructured.

As a structure, maybe it should be like this

1. The problem
2. Examples
    - Interactions Service
    - Interconnected monolith
3. Why does this happen
4. How to deal with it
    - Interactions Service
    - Interconnected monolith
5. Avoid it altogether

Some drawings would be useful as well.
Perhaps a section on DDD, talking about the problem space and how it should drive the design.

"You cannot build a replacement for something if you don't understand what it's actually doing." 

