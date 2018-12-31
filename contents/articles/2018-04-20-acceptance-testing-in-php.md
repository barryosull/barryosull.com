---
title: "Acceptance testing your PHP app with ease"
published: true
description: "A dive into the various ways you can acceptance test your apps in PHP"
tags: tdd, php, acceptance-tests
cover_image: http://barryosull.com/images/b02b4a32-fa43-456b-8b29-40692678376e.jpg
---

Acceptance tests are core to any stable system, they're how you make sure it actually works, start to finish (My preference is to write them first, use them a guideline to make sure the feature I'm writing works as expected).

When writing acceptance tests, it's best to treat the system as a [blackbox](http://softwaretestingfundamentals.com/acceptance-testing/), inputs go in and outputs go out, that's it. This proves our app works and can be interacted with by other systems. Some frameworks come with this built in, like [Laravel](https://laravel.com/), but not every app is written in those frameworks, infact most are not (especially legacy apps), and they still need to be tested.

If your app is a HTTP driven API, you'll need to test it accepts HTTP requests. So you'll need to boot up a webserver, configure it, send it HTTP requests and then check the responses. You'll probably have some console commands as well, so you'll also need to write tests for them. And let's not forget checking the database, that's an output after-all. 
How do you do all this in PHP?

# PHPs Built-in WebServer
First off, it turns out the web server part is very easy, PHP comes with [one built-in](http://php.net/manual/en/features.commandline.webserver.php) (as of PHP 5.4). Simply run `php -S 127.0.0.1:8000` at the entry point for the application and you're good to go.

Of course, there's a little more to it than that. As we're using PHPUnit for our tests (because why would you use anything else?), we want to launch the web server from our acceptance tests and then send it requests. We also want the web server to shut down once the tests have completed. 

In order to make things easier for ourselves, I've written a simple class that takes care of the above. Have a look, and I'll explain the details below.

```php
<?php
declare(strict_types=1);

namespace Root\ProjectTests\Acceptance;

use GuzzleHttp\Client;

class WebApp
{
    private $host;
    private $entryPoint;

    private static $localWebServerId = null;
    
    public function __construct(string $host, string $entryPoint) 
    {
        $this->host = $host;
        $this->entryPoint = $entryPoint;
    }

    public function startWebServer()
    {
        if ($this->isRunning()) {
            return;
        }
                
        $this->launchWebServer();
        $this->waitUntilWebServerAcceptsRequests();
        $this->stopWebserverOnShutdown();
    }
    
    private function isRunning(): bool
    {
        return isset(self::$localWebServerId);
    }

    private function launchWebServer()
    {
        $command = sprintf(
            'php -S %s -t %s >/dev/null 2>&1 & echo $!',
            $this->host,
            __DIR__.'/../../'.$this->entryPoint
        );

        $output = array();
        exec($command, $output);
        self::$localWebServerId = (int) $output[0];
    }

    private function waitUntilWebServerAcceptsRequests()
    {
        exec('bash '.__DIR__.'/wait-for-it.sh '.$this->host);
    }

    private function stopWebServerOnShutdown()
    {
        register_shutdown_function(function () {
            exec('kill '.self::$localWebServerId);
        });
    }

    public function makeClient(): Client
    {
        return new Client([
            'base_uri' => 'http://'.$this->host,
            'http_errors' => false,
        ]);
    }
}
```
This bit of code launches a web server and waits for it to start accepting requests, then it registers a shutdown function to kill the web server once all the tests have completed. We're using a script called 'wait-for-it' ([found here](https://github.com/vishnubob/wait-for-it)) that waits for the web server to go live before continuing. This was added because sometimes the tests would start before the server was actually active. We've also ensured that calling `launchWebServer` multiple times won't cause any issues. If there's a web server currently running it just stops.

Once the server is running you can call `makeClient`, which gives us a http client, specifically a Guzzle one (again, why would you use anything else), configured to send requests to that server. Now you can begin testing HTTP requests.

# Configuration
We can launch a webserver and send it requests, but how do we configure it? What database does it use, where does it log errors? You're most likely using environment variables to configure these details (and .env files to store those values). A solution could be to create different .env files for each environment, then loading the right one at runtime. This is a bit of a pain in the ass, and thankfully, it is not required.

PHPUnit has a config section for environment vars, these vars are auto loaded for each PHPUnit process. Here's an example.

```xml
<php>
    <env name="APP_ENV" value="testing"/>
    <env name="DB_NAME" value="test_db"/>
    <env name="DB_HOST" value="127.0.0.1"/>
    <env name="DB_USER" value="root"/>
    <env name="DB_PASSWORD" value=""/>
    <!-- Ohh no, my precious DB credentials, please don't hack me! -->
</php>
```

This is where things get interesting; any sub processes created by the parent test process will also have access to these env variables. In other words, the web server we launched via `exec` from our tests will automatically have all these env variables pre-loaded, so we don't have to worry about setting up any vars. It's super handy and makes testing a breeze.

# Continuous Integration
At this stage we have a web server that's configured for our local tests (through phpunit.xml). That's fine, but what happens when we want to run these acceptance tests on a CI server, such as CircleCI or TravisCI? They won't have the same config details as our local machine, so how do we configure them to work correctly?

Again, it turns out this pretty simple. [CircleCI](https://circleci.com/), for example, allows you to define environment variables in your config, which it pre-loads into the server. Now, you may think that these vars will be overwritten by PHPUnit, but don't worry, phpunit.xml env vars will not override existing env values. I.e. If you've already setup the env vars for the database through the CI boot script, then PHPUnit will leave them as is. That means the above web server will just work on the CI system, no code modification is required.

# Console commands
Of course, not all requests are HTTP, sometimes you'll want to run commands via the console. How do we configure that? Again, this is pretty simple, we run the command via `exec` from within our test code. For instance, here's a symfony style command call.

```php
exec("php bin/console app:create-user");
```
As I said previously, any process started by our tests will inherit the same env vars as the parent, so our app has everything it needs to run via `exec` (or `system` if you prefer), no changes needed.

# Data storage
Checking the response usually isn't enough for an acceptance test, you'll also need to check that right data was sent to the database. How we access the DB from within the tests? 

Yet again, this is very simple. The PHPUnit process has access to the env vars, so you can just create a PDO instance and run queries against it directly. Same with any other services, be they file storage (s3) or APIs (though this is trickier, I may need to write about this at some stage). To things even simpler, use your application container (like [PHP-DI](http://php-di.org/)) to inject these services into your test code already pre-configured. Job done.

# It's that easy
So it turns out that acceptance testing a PHP app locally is trivial. Not only that, but it's easy to run the exact same same process on a CI system, with little to no changes. At this stage I hope you're seeing the appeal, you can easily treat your system like it's a blackbox, which is a great way to get stability and confidence in your code.
