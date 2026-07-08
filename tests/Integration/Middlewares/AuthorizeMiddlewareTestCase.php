<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Integration\Middlewares;

use Illuminate\Http\Request;
use Vaened\Authorization\Tests\DatabaseTestCase;

abstract class AuthorizeMiddlewareTestCase extends DatabaseTestCase
{
    protected function requestFor(mixed $user = null): Request
    {
        $request = Request::create('/');
        $request->setUserResolver(static fn() => $user);

        return $request;
    }
}
