---
title: "Acceptance tests made easy in PHP"
published: false
description: "WIP"
tags: testing, PHP
---

Acceptance are core to any stable system, they're how you test that it actually works as expected, start to finish. When writing acceptance tests, you'll want to treat the system as a [blackbox](http://softwaretestingfundamentals.com/acceptance-testing/), inputs go in and output go out, that's it. This allows us to prove the application works as expected.

If you're building a webapp, this means you'll need to boot up a webserver, configure it, send it HTTP requests and then check the responses. How do you do this in PHP?

# PHPs Built-in WebServer
Turns out the web server part is very easy to do, PHP comes with a [web server built in](http://php.net/manual/en/features.commandline.webserver.php) (as of PHP 5.4), simply run `php -S 127.0.0.1:8000` at the entry point for the application.

Of course, there's a little more to it than that. As we're using PHPUnit for our tests (because why would you use anything else?), we want to launch the web server from our acceptance tests and then send it requests. We also want the web server to shut down once the tests have completed. 

In order to make things easier for ourselves, I've written a simple class that takes care of the above. Have a look, and I'll explain the details below.

```php
<?php
declare(strict_types=1);

namespace Root\ProjectTests\Acceptance;

use GuzzleHttp\Client;

class WebApp
{
    const HOST = '127.0.0.1:8000';
    const ENTRY_POINT = 'public/';

    private static $localWebServerId = null;

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
            self::HOST,
            __DIR__.'/../../'.self::ENTRY_POINT
        );

        $output = array();
        exec($command, $output);
        self::$localWebServerId = (int) $output[0];
    }

    private function waitUntilWebServerAcceptsRequests()
    {
        exec('bash '.__DIR__.'/wait-for-it.sh '.self::HOST);
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
            'base_uri' => 'http://'.self::HOST,
            'http_errors' => false,
        ]);
    }
}
```
This bit of code launches a web server and waits until the server is running and accepting requests, then it registers a shutdown function to kill the web server once all the tests have completed. We're using a script called 'wait-for-it' ([found here](https://github.com/vishnubob/wait-for-it)) that waits for the web server to go live before continuing. This was added because sometimes the tests would start before the server was actually active. We've also ensured that calling `launchWebServer` multiple times won't cause any issues. If there's a web server currently running it just stops.

Once the server is running you can call `makeClient`, which will create a GuzzleClient configured to send requests to that server. Now you can begin testing.

# Configuration
We can launch a webserver and send it requests, but how do we configure that application? What database does it use, where does it log errors? You're most likely using environment variables to configure these details (and .env files to store those values). A solution could be to create different .env files for each environment, then loading the right one depending at runtime. This is a bit of a pain in the ass, and thankfully, it is not required.

PHPUnit has a config section for environment vars. This will set the variables in the currently running process and the move on. Here's an example.

```xml
<php>
    <env name="APP_ENV" value="testing"/>
    <env name="DB_NAME" value="test_db"/>
    <env name="DB_HOST" value="127.0.0.1"/>
    <env name="DB_USER" value="root"/>
    <env name="DB_PASSWORD" value=""/>
</php>
```

This is where things get interesting, any sub processes created by the parent test process will have access to these env variables. In other words, the web server we launched from our tests will automatically have all these env variables pre-loaded, so we don't have to worry about setting up these vars. It's super handy and makes testing a breeze.

# CircleCI
Now we have a web server that's configured for our local machine (through phpunit.xml). That's fine, but what happens when we want to run these acceptance tests on a CI server, such as CircleCI or TravisCI? They won't have the same config details as our local machine, so how do we configure them to work correctly?

Again, it turns out this pretty simple. CircleCI allows you to define environment variables in your config, which it pre-loads into the server. The thing is, the phpunit.xml env vars will not override these values. I.e. If you've already setup the env vars for the database through the CirlceCI yaml, then PHPUnit will leave them as is. That means the above web server will just work on the CI system, no code modification is required.

# Console commands
Of course, not all requests are HTTP, sometimes you'll want to run commands via the console. Again, this is pretty simple, you just call the command via `exec` from within PHPUnit, as you would any other process. This will run the process with the same PHPUnit env vars as the host process, no changes needed.

# Database access (and other services)
For acceptance tests, you can't always rely on the response of HTTP requests, sometimes you'll need to check the values in the database and make sure they're correct. This is, yet again, very simple. The PHPUnit process has all the env vars loaded for the test DB, so you can just create a PDO instance and run queries against it directly. Same with any other services, be they file storage (s3) or APIs. You can even use the application code for this, injecting these services into your test code, pre-configured, like any other service. Job done.

# It's that easy
So it turns out that launching and testing a PHP web app locally is trivial. Not only that, but it's easy to run that same process on a CI system, with little to no changes. There really is no reason not to test your app like it's a blackbox, it's a cheap and effective way to get stability and confidence in your code.
