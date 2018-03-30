<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Support;

use Enea\Authorization\Authorizer;
use Enea\Authorization\Contracts\Authorizable;

class Helper
{
    public function authenticated(?string $guard = null): Authorizable
    {
        return auth($guard)->user();
    }

    public function authorizer(): Authorizer
    {
        return app()->make(Authorizer::class);
    }
}
