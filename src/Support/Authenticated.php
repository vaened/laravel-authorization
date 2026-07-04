<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Support;

use Vaened\Authorization\Contracts\Owner;
use Vaened\Authorization\Events\UnauthorizedOwner;
use Vaened\Authorization\Exceptions\InvalidModelException;
use Vaened\Authorization\Exceptions\UnauthorizedOwnerException;
use Vaened\Authorization\Facades\Authorizer;
use Vaened\Authorization\Facades\Helper;

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

    private function unauthorized(bool $passed, array $authorizations): void
    {
        if (! $passed) {
            $authenticated = Helper::authenticated();
            event(new UnauthorizedOwner($authenticated, $authorizations));

            throw new UnauthorizedOwnerException($authenticated);
        }
    }
}
