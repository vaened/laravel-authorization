<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Persistence;

use Illuminate\Database\Eloquent\Relations\Relation;
use Vaened\Sentinel\Identifiers;
use Vaened\Sentinel\Subject;

abstract class SubjectRepository
{
    protected function subjectId(Subject $subject): int|string
    {
        return Identifiers::value($subject->id());
    }

    protected function subjectType(Subject $subject): string
    {
        if (is_callable([$subject, 'getMorphClass'])) {
            return $subject->getMorphClass();
        }

        return (string)Relation::getMorphAlias($subject::class);
    }
}
