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

use Vaened\Authorization\Drivers\Cache\Authorizer as CacheAuthorizer;
use Vaened\Authorization\Drivers\Cache\Listeners\OperatedOnAuthorization;
use Vaened\Authorization\Events\Denied;
use Vaened\Authorization\Events\Granted;
use Vaened\Authorization\Events\Revoked;

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
