<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Resolvers;

use Vaened\Authorization\Drivers\Database\Authorizer as DatabaseAuthorizer;

class DatabaseDriverResolver extends Resolver
{
    protected function authorizer(): string
    {
        return DatabaseAuthorizer::class;
    }
}
