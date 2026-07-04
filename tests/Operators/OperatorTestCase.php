<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Operators;

use Closure;
use Vaened\Authorization\Contracts\Owner;
use Vaened\Authorization\Events\Operation;
use Vaened\Authorization\Tests\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

abstract class OperatorTestCase extends TestCase
{
    abstract protected function mainEventName(): string;

    protected function assertEvent(Owner $owner, Collection $authorizations, string $contract): void
    {
        Event::assertDispatched($this->mainEventName(), $this->checkEvent($owner, $authorizations, $contract));
    }

    private function checkEvent(Owner $owner, Collection $authorizations, string $contract): Closure
    {
        return function (Operation $event) use ($owner, $authorizations, $contract): bool {
            $this->assertInstanceOf($contract, $event->getGrantableCollection()->last());
            $this->assertCount($authorizations->count(), $event->getGrantableCollection());
            return $event->getOwner()->getIdentificationKey() === $owner->getIdentificationKey();
        };
    }
}
