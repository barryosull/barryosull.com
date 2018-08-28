<?php

namespace Tests\Browser\Pages;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class HomepageTest extends DuskTestCase
{
    /**
     * @test
     */
    public function can_see_header()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('.topnav', 1)
                ->assertSee('barryosull.com');
        });
    }

    // TODO: Write "see content" test
}
