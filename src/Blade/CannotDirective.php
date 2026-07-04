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
