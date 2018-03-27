<?php

declare(strict_types=1);

/**
 * @author enea dhack <hello@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Resolvers;

use Enea\Authorization\Drivers\Database\Authorizer as DatabaseAuthorizer;

class DatabaseDriverResolver extends Resolver
{
    protected function authorizer(): string
    {
        return DatabaseAuthorizer::class;
    }
}
