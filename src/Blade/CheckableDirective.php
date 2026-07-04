<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Blade;

use Vaened\Authorization\Contracts\Authorizable;
use Vaened\Authorization\Facades\Helper;

abstract class CheckableDirective
{
    abstract public function name(): string;

    abstract protected function check(Authorizable $authorizable, string $grantable): bool;

    public function isAuthorized(string $grantable, ?string $guard = null): bool
    {
        $authenticated = Helper::authenticated($guard);
        return $authenticated !== null ? $this->check($authenticated, $grantable) : false;
    }
}
