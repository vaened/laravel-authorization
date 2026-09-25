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

use Vaened\Authorization\SubjectType;
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
        return SubjectType::resolve($subject);
    }
}
