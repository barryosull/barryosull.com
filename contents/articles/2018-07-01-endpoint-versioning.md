---
title: When to version an API endpoint
published: false
description: description
tags: 
cover_image: http://globalnerdy.com/wordpress/wp-content/uploads/2008/07/technical_difficulties_please_stand_by.jpg
---

This article is about the difficult problem of changing and releasing a service without breaking other services that depend on it.

Say we have an existing service that we want to change. We want to be able to introduce new behaviour without breaking existing behaviour. This is a micro-service, so we should be able to deploy new features without breaking existing collaborators, specifically those that are making API calls to this service.

The solution is that for a short while, the application should offer both old and new endpoints, so that it's able to evolve and offer new features, while still being compatible with older services using the old behaviour.
Doing this allows us to release our services as they are completed, independently of other services. 

This is in keeping with the micro-service mindset, everything that is independent should have an independent lifecycle. If you need to release multiple services at the exact same time to guarantee stability, then you don't really have micro-services, you have a distributed monolith.

FYI, there are many ways to handle versioning ([see here](https://www.infoq.com/news/2017/07/versioning-event-sourcing)). For the purpose of this article we're going to look at versioning the endpoints themselves. With that sorted out, let's continue.

# When to Version

APIs change for different reasons, so before we can figure out a strategy, we need to understand what changes are breaking and require versioning.

There are six distinct types of change that can occur
- Adding a new endpoint
- Updating an endpoint
  - Adding a new attribute/s
  - Changing an attribute/s
  - Removing an attribute/s
  - Renaming an endpoint/s
- Removing an endpoint

Updating is the most complex as we must consider whether we are changing the request shape and/or the response shape. We also need to know if it's command (changes state of system) or a query (returns state of system) endpoint.

Let's gp through each of these in combination and see whether a version if needed or not.

## New endpoints:

Pretty simple, create the endpoint and label it as version 1 of that endpoint.

## Adding a new attribute/s

Command/Request: Requires a new version, as all command fields are mandatory and we want an error if one is missing

Command/Response: No new version required, append to existing response, existing client will still work

Query/Request: If field is mandatory, version. If optional, keep the same.

Query/Response: No new version required, append to existing response.

## Changing an attribute/s

New version is required in both command and query requests, as they're obviously not compatible.

If the change is in the response, then technically we don't need versioning, seeing as we can return both the old and the new data in the same endpoint, but this makes deprecating endpoints much harder. It's better to be explicit and force versioning.

## Removing an attribute/s

Command/Request: No new version required, existing clients will still send it and you can just ignore the attribute.

Command/Response: Requires a new version, as existing clients may depend on it.

Query/Request: If field is mandatory, version. If optional, keep same.

Query/Response: Requires a new version for the same reason above.

## Renaming an endpoint/s

Simple strategy, keep the old endpoint, then treat the renamed endpoint as a new one.

## Remove an endpoint

Assuming the endpoint is no longer used, just delete it. 

# How to Version 

## Strategy

As we have an ever evolving API, we need a clear cut strategy for versioning.

At a high level, there are two categories of versioning:
- Explicit
- Implicit

**Explicit**: When a breaking change is introduced, version that endpoint to highlight the change

**Implicit**: Accept both old and new payloads, figure out what to do based on the shape of the incoming payload

I suggest going with **Explicit** for the following reasons
- Easy to broadcast endpoints and their request/response shapes
- Easy to see what versions are active
- Easy to remove old endpoints

## Implementation

Now that we know when we need to version, let's talk about how we do it. 

Let's assume that we version specific use case - `DoSomething`.

By default the folder should be prefixed with V1 - e.g `DoSomethingV1`. A new version use will have `DoSomethingV2` name.

If new version is intended to work with new payload shape we should create a `PayloadUpgrader` class within the same folder.

If versioned usecase's domain code changes the output contract and we need to support old response shape we should have `ResponseDowngrader` class within same folder (It will be injected to the `ResponseFactory`).

Also we might need to  have `ResponseDowngrader` for the V1 use case for backward compatibility.

As mentioned above we should append appropriate version to the route. (By default v1).

E.g.:

```
'/api/latest/agent/retail/quoting/trip/start/v1'
'/api/latest/agent/retail/quoting/trip/start/v2'
```

Folder structure:

![versioned_1.png](files/78c0ae9a-098a-41c1-81eb-f4c9d91600e4/versioned_1.png)

Tests also should be versioned in the same way. We should prepare command payload fixtures for both new and old versions.

Test case structure example (versioned command):

![versioned_3.png](files/48aa0ec7-c642-47d6-8815-b562e6f4ee6c/versioned_3.png)

If it is expected that endpoint returns some response back it should be the test with appropriate assertion to match response payload.

This statement is also applied to the error responses.

Test case structure example (query service uses seeders to populate db):

![versioned_2.png](files/9f53491b-31d7-4522-8f4d-52b81592f388/versioned_2.png)

If we need to deny access to the particular endpoint we can use TestOnlySecurity middleware on the routes.

See [https://github.com/dynamicreservations/retail/blob/develop/app/Http/Middleware/TestOnlySecurity.php](https://github.com/dynamicreservations/retail/blob/develop/app/Http/Middleware/TestOnlySecurity.php)

Note: New intend to use JSON-schema validation to prevent invalid or malformed payloads sent to server as a command(query possibly) and checking the output payload for contract consistency.