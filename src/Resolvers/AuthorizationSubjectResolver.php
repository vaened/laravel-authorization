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

use Illuminate\Http\Request;
use Vaened\Sentinel\Subject;

interface AuthorizationSubjectResolver
{
    public function resolve(object|null $user, Request $request): Subject|null;
}
