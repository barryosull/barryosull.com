---
title: title
published: false
description: description
tags: tags
cover_image: http://globalnerdy.com/wordpress/wp-content/uploads/2008/07/technical_difficulties_please_stand_by.jpg
---
This is a retrospective on integrating Microservice with a Legacy codebase.

Short version, we wanted to integrate a microservice with our existing legacy application and completely remove the old functionality. This did not go as well as we had hoped, mostly due to the quality of the code, and if I'm quite honest, our own hubris. We figured out how to do it safely in the end, but not before we learned some hard lessons. So that's what this is, a breakdown of what we did and what we learned, hopefully it will help others from making the same mistakes.

Background:
We had created a view counting micro-service called Digest. It was created to replace a faulty view counting system in the Legacy Monolith, which we call API. Digest was correctly tallying up views and we had successfully integrated part of it with API. Now articles returned by API had view data from Digest, i.e. articles displayed in the app were showing the more accurate view count from Digest, as opposed to the incorrect values that API was previously calculating and returning.

However, we weren't done, API still had the old view calculation code, which was populating a column in a table. So any queries/reports that relied on views were still using this inaccurate view data.

The goal:
Our goal was to switchover to using Digest for all view data, and completely remove the old view calculating system from API. Initially we thought this was quite simple, as the column wasn't used in a lot of places, so we dove right in.

Attempt 1: The big bang removal
We removed the code that populated the old views and went through the system, looking for were the column was used. Anywhere that depended on the `view` column had the default set to 0. The tests passed, the code was reviewed and given a thumbs up, we thought we were good to go and released it.

Why it failed:
Turns out we had inadvertently broken a report in the system. The report relied on the `view` column being populated, but due to the messy nature of the SQL we didn't notice. We had assumed the existing tests would catch any issues, but there were no tests for that feature of the system.

So we reverted the PR. The old system started populating views, but we had lost views during the time that other system was deployed. So we had to write a script to copy over values from digest and into the post_stats table. Took some time but we got it all working again. Now were at square one codewise, but we had a deeper understanding of the areas affected, we felt we could actually replace it now.

Attempt 2: Populate report data from Digest
This time round we knew that certain sections of the app relied on views from Digest. We added an endpoint to Digest that allowed us to fetch a collection of views based on their IDs. Anywhere in the app that needs to display views would now get them via this endpoint.

We also removed anywhere that referenced the `view` column, searching for usages of it in the app and removing anywhere not explicitly used. For example, there was a Search class that ordered by view, but no one knew if this was every used and no tests indicated it did, so we just removed that sort by and waited to see what happened.

This worked, until we noticed that "most popular articles" feature was showing the wrong articles. Whoops. Turns out it was ordering articles by view and it was the only place in the app that needed to sort by view. It was triggered by an API call from Front, that told it to order by `views`. Only place that did that.

The feature wasn't a priority, but we got the fix/solution out pretty fast. We used the same fetch collection endpoint and digest, then sorted the result by view count in memory. Wrote some tests, they passed, so we released it and considered this a job well done.

Why it failed:
After a few days we discovered some issues. The reports we fine, but "Most Popular" only kind worked, it wasn't 100% right. Worse thiugh, Front had lots of articles were the view count wasn't changing. The rivers were fine, they showed sensible view counts, but when you went into the article itself, it was stuck with a view count between 2 and 6, which was clearly wrong. We had gotten complaints from multiple people in the office about this, and it looked really bad.

So what happened? It turns out that Front was caching articles forever (to save on API calls to API most likely). This causea an issues if you think about it, if the article is never cleared from the cache, how does it get updated stats? The solution to this it turns out, implemented in API, was to look for changes in the `post_stats` table and then push those changes to Front. It did this via a cron that ran every three minutes. It didn't affect all articles, as anything in a popular river would get force refreshed, buy anything old or in magazine would not get updated.

Turns out that this bug was also present in Attempt 1, but no one noticed and reported it before we reverted, inadvertently fixing the issue.

The thing is, none of us really knew that this was how things worked, we didn't know of this behaviour. Some of us knew that API made HTTP calls to Front, but they didn't know why. Others noticed the use of a `synced` field that was updated at the same time as views (this was used to mark stats that should be synced) but never twigged that this was used to trigger work, rather than just a number displayed in a report.

In hindsight all the pieces were there in the code, we just didn't realise it. I could say that we should have been more diligent in checking, but how do you do that? There was no one that knew about this feature, not really, and inferring it from SQL queries distributed throughout the codebase was not going to happen just by looking at the code, it was too messy.

So we reverted the PR, fixed the values manually via a script (a new had to be written as the old one was not commited) and then tried again.

Attempt 3: The graceful refactor
Up till now we were doing grand rewrites and assuming that would safely replace the feature in one fell swoop. That clearly wasn't working. We clearly didn't understand the system well enough to make big changes and it would be foolish to think we had discoverd every place we need to changes.

So instead we decided to go slow, step by step, refactoring the system and making small stable changes.

This time round we didn't touch the old code that populated views, we kept it as is. We also decided not to remove the use of the `views` column, it was too core to the system and we weren't confident we could untangle it safely (see our failures above for why). So our goal now was to populate that column with digest data. 

As we wanted to do this safely, we sought to gain understanding of how `post_stats` Firstly we refactored the system, encapsulating all logic that modified the `post_stats` table. We did 

Lessons learned:
It is never safe to change legacy code. Introducing a change, even one we're confident will work, will most likely cause issues. You're uncovering areas of ignorance in your understanding of the system and you will always have those. So instead of trying to make the code "perfect", try write code that changes as little as possible, and make it easy to switch behaviour back to the old, working system.