---
title: "Acceptance tests made easy in PHP"
published: false
description: WIP"
tags: testing, PHP
---

When writing acceptance tests, you'll want to treat the system as a blackbox, inputs go in and output go out, that's it. This allows us to prove the application works as expected, otherwise, how would we know?

If you're building a webapp, this means you'll want to boot up a webserver, send it actual HTTP requests and check the response. How do you do this in PHP?

# PHPs Built-in WebServer
Turns out this is very easy to do, PHP comes with a webserver built in, simply run `php -S 127.0.0.1:8000` at the entry point for the application.

Of course, there's a little more to it than that. As we're using PHPUnit for our tests (because why would you use anything else?), we want to launch the webserver from our acceptance tests and then send it requests. We also want the webserver to shut down once the tests have completed. 

In order to make things easier for ourselves, I've written a simple class that takes care of the above. Have a look, and I'll explain the details below.

```php
<?php
declare(strict_types=1);

namespace Root\Project\Acceptance;

use GuzzleHttp\Client;

class WebApp
{
    const HOST = '127.0.0.1:8000';
    const ENTRY_POINT = 'public/';

    private static $localWebServerId = null;

    public function startWebServer()
    {
        $this->launchWebServer();
        $this->waitUntilWebServerAcceptsRequests();
        $this->stopWebserverOnShutdown();
    }

    private function launchWebServer()
    {
        if (isset(self::$localWebServerId)) {
            return;
        }

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
This bit of code launches a webserver and waits until the server is running and accepting requests, then it registers a shutdown function to kill the webserver once all the tests have completed. We're using a script called 'wait-for-it' ([found here](https://github.com/vishnubob/wait-for-it)) that waits for the webserver to go live. This was added because sometimes the tests would start before the server was actually active. We've also ensured that calling `launchWebServer` multiple times won't cause any issues. If there's a webserver currently running it just stops.

Once the server is running you can call `makeClient`, which will create a GuzzleClient configured to send requests to that server. Now you can begin testing.

# Environment variables
We can launch a webserver and send it requests, but how do we configure that web server? You're most likely using environment varaibles for configuration (and .env files to store those values), so this can lead to us creating different .env files for each environment, then loading the right one depending on the runtime. This is a bit of a pain in the ass, and thankfully, it is not required.

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

Here's where things get interesting, any sub processes created by the test process will have access to ehse env variables. In other words, the webserver we launched from our tests will automatically have all these varaibles pre-loaded, so we don't have to worry about setting up these vars. It's super handy and makes testing a breeze.

# CircleCI
Now we have a webserver that's configured for our local machine (through phpunit.xml). That's fine, but what happens when we want to run these acceptance tests on a CI server, such as CircleCI or TravisCI? They won't have the same config details as our local machine, so how do we configure them to work correctly?

Again, it turns out this pretty simple. CircleCI allows you to define environment varaibles in your config, which it preloads into the server. The thing is, the PHPunit.xml env vars will not override these values. I.e. If you've already setup the env vars for the database through the CirleCI xml, then PHPunit won't override them. That means the above webserver will just work on the CI system, no code modification is required.

# Console commands
Of course, not all requests are HTTP, sometimes you'll want to run commands via the console. Again, this is pretty simple, you just call the command via `exec` as you would any other process. This will run the process with the same PHPunit env vars as the host process, no changes needed.

# Database access
For acceptance tests, you can't always rely on the response of HTTP requests, sometimes you'll need to check the values in the database and make sure they're correct. This is, yet again, very simple. The PHPUnit process has all the env vars loaded for the test DB, so you can just create a PDO instance and running queries against it directly. Job done.

# It's that easy
So it turns out that launching and testing a PHP web app locally is trivial. Not only that, but it's easy to run that same process on a CI system, with little to no changes. There really is no reason not to test your app like it's a blackbox, and PHP makes that incredibly easy.