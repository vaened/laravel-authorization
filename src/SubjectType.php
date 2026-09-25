<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization;

use Illuminate\Database\Eloquent\Relations\Relation;
use Vaened\Sentinel\Subject;

final class SubjectType
{
    public static function resolve(Subject $subject): string
    {
        if (method_exists($subject, 'getMorphClass')) {
            return $subject->getMorphClass();
        }

        return (string)Relation::getMorphAlias($subject::class);
    }
}
