<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Support\Resolvers;

use Illuminate\Http\Request;
use Vaened\Authorization\Resolvers\AuthorizationSubjectResolver;
use Vaened\Authorization\Resolvers\SubjectResolution;
use Vaened\Sentinel\Subject;

final class CountingSubjectResolver implements AuthorizationSubjectResolver
{
    public static int $calls = 0;

    public static function reset(): void
    {
        self::$calls = 0;
    }

    public function resolve(object|null $user, Request $request): SubjectResolution
    {
        self::$calls++;

        return $user instanceof Subject ? SubjectResolution::found($user) : SubjectResolution::notFound();
    }
}
