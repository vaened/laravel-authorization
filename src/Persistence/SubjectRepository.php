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

use Illuminate\Database\Eloquent\Model;
use Vaened\Authorization\Errors\UnsupportedSubject;
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
        return $this->subject($subject)->getMorphClass();
    }

    protected function subject(Subject $subject): Model
    {
        if ($subject instanceof Model) {
            return $subject;
        }

        throw UnsupportedSubject::becauseItDoesNotExtendModel($subject::class);
    }
}
