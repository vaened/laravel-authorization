<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization;

use Vaened\Authorization\Events\UnauthorizedOwner;
use Vaened\Authorization\Listeners\WriteUnauthorizedLog;
use Vaened\Authorization\Support\Determiner;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as BaseEventServiceProvider;

class EventServiceProvider extends BaseEventServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function listens()
    {
        return [
            UnauthorizedOwner::class => $this->getUnauthorizedOwnerListeners(),
        ];
    }

    private function getUnauthorizedOwnerListeners(): array
    {
        $listeners = array();

        if (Determiner::listenUnauthorizedOwnerEventForLogger()) {
            $listeners[] = WriteUnauthorizedLog::class;
        }

        return $listeners;
    }
}
