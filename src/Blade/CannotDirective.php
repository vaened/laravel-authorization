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

class CannotDirective extends CanDirective
{
    protected function check(Authorizable $authorizable, string $grantable): bool
    {
        return ! parent::check($authorizable, $grantable);
    }

    public function name(): string
    {
        return 'authenticatedCannot';
    }
}
