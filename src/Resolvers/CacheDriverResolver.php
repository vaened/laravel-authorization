<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Resolvers;

use Enea\Authorization\Drivers\Cache\Authorizer as CacheAuthorizer;
use Enea\Authorization\Drivers\Cache\Listeners\OperatedOnAuthorization;
use Enea\Authorization\Events\Denied;
use Enea\Authorization\Events\Granted;
use Enea\Authorization\Events\Revoked;

class CacheDriverResolver extends Resolver
{
    protected function authorizer(): string
    {
        return CacheAuthorizer::class;
    }

    protected function listens(): array
    {
        return [
            Granted::class => [
                OperatedOnAuthorization::class,
            ],
            Revoked::class => [
                OperatedOnAuthorization::class,
            ],
            Denied::class => [
                OperatedOnAuthorization::class,
            ],
        ];
    }
}
