<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Support;

use Enea\Authorization\Contracts\Owner;
use Enea\Authorization\Events\UnauthorizedOwner;
use Enea\Authorization\Exceptions\InvalidModelException;
use Enea\Authorization\Exceptions\UnauthorizedOwnerException;
use Enea\Authorization\Facades\Authorizer;
use Enea\Authorization\Facades\Helper;

class Authenticated
{
    public function can(string ...$permissions): void
    {
        $this->validModel();
        $this->unauthorized(Authorizer::canAny(Helper::authenticated(), $permissions), $permissions);
    }

    public function is(string ...$roles): void
    {
        $this->validModel();
        $this->unauthorized(Authorizer::isAny(Helper::authenticated(), $roles), $roles);
    }

    private function validModel(): void
    {
        if (! Helper::authenticated() instanceof Owner) {
            throw new InvalidModelException();
        }
    }

    private function unauthorized(bool $passed, array $permissions): void
    {
        if (! $passed) {
            $authenticated = Helper::authenticated();
            event(new UnauthorizedOwner($authenticated, $permissions));
            throw new UnauthorizedOwnerException($authenticated);
        }
    }
}
