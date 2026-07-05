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

use Vaened\Authorization\Authorizer;
use Vaened\Authorization\Contracts\Owner;
use Vaened\Authorization\Events\UnauthorizedOwner;
use Vaened\Authorization\Exceptions\InvalidModelException;
use Vaened\Authorization\Exceptions\UnauthorizedOwnerException;

class Authenticated
{
    public function __construct(
        private readonly Helper $helper,
        private readonly Authorizer $authorizer
    ) {
    }

    public function can(string ...$permissions): void
    {
        $this->validModel();
        $authenticated = $this->authenticated();
        $this->unauthorized($this->authorizer->canAny($authenticated, $permissions), $permissions);
    }

    public function is(string ...$roles): void
    {
        $this->validModel();
        $authenticated = $this->authenticated();
        $this->unauthorized($this->authorizer->isAny($authenticated, $roles), $roles);
    }

    private function validModel(): void
    {
        if (! $this->authenticated() instanceof Owner) {
            throw new InvalidModelException();
        }
    }

    private function unauthorized(bool $passed, array $authorizations): void
    {
        if (! $passed) {
            $authenticated = $this->authenticated();
            event(new UnauthorizedOwner($authenticated, $authorizations));

            throw new UnauthorizedOwnerException($authenticated);
        }
    }

    private function authenticated(): ?Owner
    {
        $authenticated = $this->helper->authenticated();
        return $authenticated instanceof Owner ? $authenticated : null;
    }
}
