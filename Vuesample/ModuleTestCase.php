<?php

namespace Tests\Browser\Backend;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use App\Models\[UNAME] as Module;

class [UNAME]Test extends DuskTestCase
{
    /**
     * A basic browser test example for [UNAME] Module....
     * @group [MODULE]
     * @return void
     */
    public function test[UNAME]() {
        $this->browse(function (Browser $browser) {
            $user = User::first();

            $browser->pause(3000);

            $browser->loginAs($user)
                    ->visit(route('[MODULE].create'));

            $browser[TESTCASEDATA]
                    ->press('Save')
                    ->assertRouteIs('[MODULE].create')
                    ->pause(3000)
                    ->assertSee('[ULABEL] Created!')
                    ->visit(route('[MODULE].index'));

            $model = Module::latest()->first();
            $browser->click('.custom-default')
                    ->clickLink("Edit")
                    ->visit(route('[MODULE].edit', $model->id))
                    ->assertSee('Edit [ULABEL]')
                    [TESTCASEDATA]
                    ->press('Save')
                    ->assertRouteIs('[MODULE].edit', $model->id)
                    ->pause(3000)
                    ->assertSee('[ULABEL] Updated!')
                    ->visit(route('[MODULE].index'))
                    ->pause(2000)
                    ->click('.custom-default')
                    ->clickLink("Delete")
                    ->press("Yes Delete it!")
                    ->pause(3000)
                    ->assertSee('[ULABEL] Deleted!');
        });
    }
}
