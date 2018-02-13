---
title: Writing a DSL parser using PegJS
published: true
description: Write a simple parser using PegJS
tags: DSL
cover_image: http://barryosull.com/images/0bd858fa-5347-4b09-9f5f-2ec5f0bd1de4.png
---

In the [previous article](/blog/write-dsls-and-code-faster/) I wrote about Domain Specific Languages (DSLs) and how useful they are, but I didn't get into the details of parsing them, that's where this article comes in.

Previously we made this DSL:
```javascript
User.ScheduleAppointment has { 
  a UserId userId 
  an AppointmentDatetime appointmentDatetime
  a Location location from {
    a LocationName locationName from location
    a Latitude latitude
    a Longitude longitude
  }
}
```

We want to take the above, and parse it. That means turning the text into an Abstract Syntax Tree (AST). An AST is a tree structure that's easy to navigate and interpret. Once we have an AST, we can interpret it.

To do this, we're going to use [PegJS](http://pegjs.org/), a Parsing Expression Grammar (PEG) parser written in Javascript. PegJS (like most parsers) is based on regular expressions, they allow you to build named regexes (rules) that you combine together to form a tree. The results of your rules can be turned into data structures, letting you build up your AST.

# The AST
The first thing we need to do is design our AST, as we need to know what the end result looks like. Once we know this, we can reverse engineer the rules. 

So in our perfect world, we want to take the DSL above and turn it into the following.
```json
{
   "entity": "User",
   "command": "ScheduleAppointment",
   "values": [
      {
         "class": "UserId",
         "param": "userId",
         "alias": null,
         "type": "value"
      },
      {
         "class": "AppointmentDatetime",
         "param": "appointmentDatetime",
         "alias": null,
         "type": "value"
      },
      {
         "class": "Location",
         "param": "location",
         "type": "composite",
         "values": [
            {
               "class": "LocationName",
               "param": "locationName",
               "alias": "location",
               "type": "value"
            },
            {
               "class": "Latitude",
               "param": "latitude",
               "alias": null,
               "type": "value"
            },
            {
               "class": "Longitude",
               "param": "longitude",
               "alias": null,
               "type": "value"
            }
         ]
      }
   ]
}
```
This structure is easy to navigate and interpret, so it's a great end goal for our parser. Now we know what we want, let's figure out how to get there. 

# Writing a PEG 
PEGs (and most parsers) work via regular expressions (regexes). If you're like me, then your regex-fu is probably a bit weak, so writing a parser can seem like a daunting task. Thankfully, there are easy ways to learn regexes. I'd recommend playing this [regex crossword game](https://regexcrossword.com/). Once you've completed the "experienced" level crossword, you'll understand regexes well enough that you'll be able to write a parser without looking up regex documentation. I'd highly recommend this game to anyone that wants to learn regexes.

Assuming we understand Regular Expressions, here's an example of single simple rule.

## Sample PegJS rule
```javascript
Var = name:[A-Za-z0-9_]*
  {
    return name.join("");
  }
```

The above is a PegJS rule that matches variable names like the following "positionId", "canidateId", "variable_name", etc... .
It then returns the result as a string. Here this is defined as a "rule" called `Var` that can be reused throughout the parser, that way we don't have to repeat code, making the parser easier to read and use.

## The rules
A PegJS parser is made up of rules. Our goal is to take the above DSL and figure out the rules for each type of AST structure. Rules are composable, so once we have a few basic rules, we can start building more complex ones. 

1. Whitespace (_): match all the spaces, newlines and tabs, usually ignored
1. Var: match valid variable names
1. Alias: An alias (Var) for a request parameter, optional
1. Value: A composite of a class (Var), a param name (Var) and an optional alias (Var)
1. CompositeValue: A composite of a class (Var), a param name (Var) and a collection of values
1. Values: a collection of Values and CompositeValues
1. Command: a composite of an entity (Var), a command (Var) and a collection of values (Values)

Now we know what the object types are, we can write the PEGJs rules to parse the DSL and create the AST.

# The full PEG

```javascript
/**********
 Starting rule, our entry point to the parser.
 The first part of the PEG extracts the entity name as a string, 
 and makes the "entity" accessible in the JS below, allowing us to the AST and return it. 
 It matches certain expected keywords, eg. "has", but does nothing with them. 
 Finally it extracts the values, which have already been turned into a Values AST. 
 You'll see the "_" rule used here, this means "accept/skip whitespace", it's defined below.
**********/
Command = entity:Var "." command:Var _ "has" _ "nothing"* _ values:Values* _
  {
    // Handle case that there are no values
    values = values.length === 0 ? [] : values[0];

    // Return the matched results with this object structure
    return {
      entity: entity,
      command: command,
      values: values
    }
  }
 
/**********
 Matches a collection of inputs, 0 to many, that are wrapped in parentheses
**********/
Values = "{" _ values:(CompositeValue/Value)* _ "}"
  {
    return values;
  }

/**********
 Value and CompositeValues always have the same initial shape, so I extracted 
 this into a partial result that is extended by both Value and Composite
**********/
ValuePartial = _ "a" [n]? _ cls:Var _ name:Var _
  {
    return {
      class: cls,
      param: name
    }
  }

Value = value:ValuePartial alias:(Alias)? _
  {
    value.requestParam = (alias) ? alias: value.param;
    value.type = 'value';
    return value;
  }
 
/**********
 Extract the alias value, ignore the "from" string
**********/   
Alias = _ "from" _ alias:Var 
  {
    return alias;
  }
  
CompositeValue = value:ValuePartial "from" _ values:Values
  {
  	value.type = 'composite';
    value.values = values;
    return value;
  }

Var = name:[A-Za-z0-9_]*
  {
    return name.join("");
  }

/**********
 Match any sequence of "whitespace" characters
**********/   
_ = [ \t\n\r]*
```

That's the full parser. The above will turn our DSL into the AST above.
You can check this out yourself. Simply go to the PegJS, open their [online editor](http://pegjs.org/online) and paste the above DSL and parser in. You'll see the results straight away.

As a side note, we're using a PegJS extension that outputs a PHP version of the parser, so we can use the same parser on the server as well as the client.

## Conclusion
As you can see writing a parser isn't that hard. Using that simple parser grammar, I'm able to automate part of my teams workload, ie, writing boilerplate (and error prone) adapters that turns HTTP requests into commands. These simple DSLs make that trivial.

After seeing the above in action, I hope you're thinking of all the things you could define and automate with a DSL. So why not write a simple DSL and parser, and try it out?
