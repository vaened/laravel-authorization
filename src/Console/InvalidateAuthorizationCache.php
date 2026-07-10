<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Console;

use Illuminate\Console\Command;
use Vaened\Sentinel\Cache\AuthorizationCacheStore;

final class InvalidateAuthorizationCache extends Command
{
    protected $signature   = 'authorization:cache:invalidate';

    protected $description = 'Invalidate the Laravel Authorization cache globally';

    public function handle(AuthorizationCacheStore $store): int
    {
        $store->invalidate();

        $this->info('Authorization cache invalidated.');

        return self::SUCCESS;
    }
}