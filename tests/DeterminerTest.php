<?php
/**
 * Created on 18/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests;

use Enea\Authorization\Support\Determiner;

class DeterminerTest extends TestCase
{
    public function test_the_default_listeners_are_active(): void
    {
        $this->app->make('config')->set('authorization', null);
        $this->assertTrue(Determiner::listenUnauthorizedOwnerEventForLogger());
        $this->assertTrue(Determiner::transformSecretNameToKebabCase());
    }

    public function test_its_loading_custom_listeners(): void
    {
        $this->loadCustomListenerConfig();
        $this->assertFalse(Determiner::listenUnauthorizedOwnerEventForLogger());
        $this->assertFalse(Determiner::transformSecretNameToKebabCase());
    }

    private function loadCustomListenerConfig(): void
    {
        $this->app->make('config')->set('authorization', [
            'authorizations' => [
                'transform-secret-name-to-kebab-case' => false,
            ],
            'listeners' => [
                'unauthorized-owner-logger' => false,
            ],
        ]);
    }
}
