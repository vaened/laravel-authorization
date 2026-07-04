<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Blade;

use Vaened\Authorization\Contracts\Authorizable;
use Vaened\Authorization\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

abstract class DirectiveTestCase extends TestCase
{
    public function compile(string $view, array $params): string
    {
        Artisan::call('view:clear');
        return trim(view()->make("directives.{$view}", $params)->render());
    }

    protected function getLoggedUser(): Authorizable
    {
        $user = $this->user();
        $this->actingAs($user);
        return $user;
    }
}
