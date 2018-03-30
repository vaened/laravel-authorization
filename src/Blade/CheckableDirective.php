<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Blade;

use Enea\Authorization\Contracts\Authorizable;
use Enea\Authorization\Facades\Helper;

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
