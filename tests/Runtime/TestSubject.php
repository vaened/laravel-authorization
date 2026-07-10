<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Runtime;

use Vaened\Sentinel\Identifier;
use Vaened\Sentinel\Subject;

final readonly class TestSubject implements Subject
{
    public function __construct(private int $id)
    {
    }

    public function id(): int|string|Identifier
    {
        return $this->id;
    }
}