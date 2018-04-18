<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Tests;

use Enea\Authorization\Support\Determiner;

class DeterminerTest extends TestCase
{
    public function test_the_default_config_are_active(): void
    {
        $this->app->make('config')->set('authorization', null);
        $this->assertTrue(Determiner::listenUnauthorizedOwnerEventForLogger());
        $this->assertTrue(Determiner::transformSecretNameToKebabCase());
    }

    public function test_its_loading_custom_config(): void
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
