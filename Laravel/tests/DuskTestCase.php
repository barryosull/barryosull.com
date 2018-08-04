<?php

namespace Tests;

use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        static::startChromeDriver();
    }

    const BASE_URL = 'http://localhost:9515';

    private static $driver;
    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        if (!self::$driver) {
            $options = (new ChromeOptions)->addArguments([
                '--disable-gpu',
                '--headless'
            ]);

            self::$driver = RemoteWebDriver::create(
                self:: BASE_URL,
                DesiredCapabilities::chrome()->setCapability(
                    ChromeOptions::CAPABILITY, $options
                )
            );
        }

        return self::$driver;
    }
}
