---
title: Writing a DSL parser using PegJS
published: false
description: Write a simple parser using PegJS
tags: DSL, parsing, pegjs
---

In the [previous article](/blog/write-dsls-and-code-faster/) I wrote about Domain Specific Language (DSL) and how useful they are, but I didn't get into the details of parsing them. That's because it's a complex topic, so I wanted to explain it properly. Thus, here's a full article, detailing the parser and how it was built.

Previously we made this DSL:
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

Now we want to parse it and turn into an Abstract Syntax Tree (AST). This is a structure that turns the above text into a tree structure that's easy to navigate and interpret. Think of it as the first stage in interpreting the DSL.

To do this, I used [PegJS](http://pegjs.org/), a PEG parser written in Javascript. PegJS (like most parsers) is based on regular expressions, they allow you to build named regexes (rules) that you combine together to form a tree. The results of your rules can be turned into data structures, letting you build up your AST.

### Regexs are hard
If you're like me, then you're regex-fu is probably a bit weak, so writing a parser can seem like a daunting task. Thaknfully, there are easy ways to learn regexes. I'd recommend playing this [regex crossword game](https://regexcrossword.com/). Once you've completed the "experienced" level crossword, you'll understand regexes well enough that you'll be able to write a parser without looking up regex documentation. I'd highly recommend this game to anyone that wants to learn regexes.

Assuming we understand Regular Expressions, here's an example of single simple rule.

### Sample PegJS rule
```
Var = name:[A-Za-z0-9_]*
  {
    return name.join("");
  }
```

The above is a PegJS rule that matches variable names like the following "positionId", "canidateId", "variable_name".
It then returns the result as a string. Here this is defined as a "rule" called `Var` that can be reused throughout the parser, that way I don't have to repeat code, making the parser easier to read and use.

### The rules
A PegJS parser is made up of rules. In the DSL above, there are distinct components, some are simple strings, others are a composite of other components.

0. Whitespace: match all the spaces, newlines and tabs.
0. Var: match valid variable names
0. Command: which are made from the sub-domain, the command name (which are both vars) and the inputs
0. SingleLineInput, an input that only needs a single request value, made up a valueobject, an input name and a possible alias
0. MultiLineInput, same as the above (excluding alias) with multiple child inputs.
0. Alias, an aliased request name that may not exist, for when the param name is different to the input name
0. Input: a collection of SingleLineInputs and MultiLineInputs

That's the rule set, based on the above, we can now write our parser.

### The full parser

```
// Starting rule
// "subDomain:Var" defines the rule type on the right, and the resulting value on the left
// These are then referenced in the return statement (defined below the regex) to create the outputted parser tree.
Command = subDomain:Var "." name:Var _ "has" _ "nothing"* _ inputs:Inputs* _
  {
    // Handle case that there are no inputs
    inputs = inputs.length === 0 ? [] : inputs[0];

    // Return the matched results with this object structure
    return {
      subDomain: subDomain,
      command: name,
      inputs: inputs
    }
  }
 
//Matches a collection of inputs, 0 to many, that are wrapped in parentheses
Inputs = "{" _ inputs:(MultiLineInput/SingleLineInput)* _ "}"
  {
    return inputs;
  }

//Single and Multi line inputs always have the same initial shape, so I extracted this into a partial result that is extended
InputPartial = _ "a" [n]? _ valueObject:Var _ name:Var _
  {
    return {
      type: "valueObject",
      valueObject: valueObject,
      name: name
    }
  }

SingleLineInput = input:InputPartial alias:(Alias)? _
  {
    var alias = (alias) ? alias[0] : input.name;
    input.inputs = [
      {
        type: "parameter",
        name: alias
      }	
    ];
    return input;
  }
    
Alias = _ "from" _ alias:Var 
  {
    return alias;
  }
  
//The same inputs rule is used again to allow for recursive parsing inputs
MultiLineInput = input:InputPartial "from" _ inputs:Inputs
  {
    input.inputs = inputs;
    return input;
  }

Var = name:[A-Za-z0-9_]*
  {
    return name.join("");
  }

_ = [ \t\n\r]*
```

That's the full parser. The above will turn our DSL into the following.

```
{
   "subDomain": "User",
   "command": "ScheduleAppointment",
   "inputs": [
      {
         "type": "valueObject",
         "valueObject": "UserId",
         "name": "userId",
         "inputs": [
            {
               "type": "parameter",
               "name": "userId"
            }
         ]
      },
      {
         "type": "valueObject",
         "valueObject": "Appointmentatetime",
         "name": "appointmentDatetime",
         "inputs": [
            {
               "type": "parameter",
               "name": "appointmentDatetime"
            }
         ]
      },
      {
         "type": "valueObject",
         "valueObject": "Location",
         "name": "location",
         "inputs": [
            {
               "type": "valueObject",
               "valueObject": "LocationName",
               "name": "locationName",
               "inputs": [
                  {
                     "type": "parameter",
                     "name": "l"
                  }
               ]
            },
            {
               "type": "valueObject",
               "valueObject": "Latitude",
               "name": "latitude",
               "inputs": [
                  {
                     "type": "parameter",
                     "name": "latitude"
                  }
               ]
            },
            {
               "type": "valueObject",
               "valueObject": "Longitude",
               "name": "longitude",
               "inputs": [
                  {
                     "type": "parameter",
                     "name": "longitude"
                  }
               ]
            }
         ]
      }
   ]
}
```

You can check this out yourself. Simply go to the PegJS, open their [online editor](http://pegjs.org/online) and paste the above DSL and parser in. You'll see the results straight away.

### Using the AST
Now that we have a parser, it's very easy to write the code that loops through the following and builds up our commands. The above structure is incredibly simple to navigate. You could put the results into a NoSQL DB if you'd like, allowing for complex searches and views on the data.

As a side note, we're using a PegJS extension that outputs a PHP version of the parser, so we can use the same parser on the server as well as the client.

### That's that
As you can see, writing a parser isn't that hard, it's nowhere near as daunting and scary as I first imagined. Using that simple parser grammar, I'm able to automate large chunks of my team's workload, at very little up front dev costs.

After seeing that, I hope you're thinking of all the things you could define and automate with a DSL. So why not write a simple DSL and parser, and try it out?
