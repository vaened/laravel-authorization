<?php
/**
 * Created on 18/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests;

use Enea\Authorization\Support\Listeners;

class ListenersTest extends TestCase
{
    public function test_the_default_listeners_are_active(): void
    {
        $this->app->make('config')->set('authorization', null);
        $this->assertEquals(Listeners::listenUnauthorizedOwnerEventForLogger(), true);
    }

    public function test_its_loading_custom_listeners(): void
    {
        $this->loadCustomListenerConfig();
        $this->assertEquals(Listeners::listenUnauthorizedOwnerEventForLogger(), false);
    }

    private function loadCustomListenerConfig(): void
    {
        $this->app->make('config')->set('authorization.listeners', [
            'unauthorized-owner-logger' => false,
        ]);
    }
}
