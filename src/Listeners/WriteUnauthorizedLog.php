<?php
declare(strict_types=1);

/**
 * Created on 10/03/18 by enea dhack.
 */

namespace Enea\Authorization\Listeners;

use Enea\Authorization\Events\UnauthorizedOwner;
use Illuminate\Database\Eloquent\Model;
use Psr\Log\LoggerInterface;

class WriteUnauthorizedLog
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(UnauthorizedOwner $event): void
    {
        $this->logger->alert($this->makeMessage($event));
    }

    private function makeMessage(UnauthorizedOwner $event): string
    {
        $message = implode(' ', array_filter([
            $this->getHeaderMessage($event->getAuthorizable()),
            $this->getBodyMessage($event->getGrantables()),
        ]));

        return "[UNAUTHORIZED] {$message}";
    }

    private function getHeaderMessage(Model $model): string
    {
        return "The {$model->getTable()} model with identification {$model->getKey()} does not have the necessary authorization.";
    }

    private function getBodyMessage(array $grantables): ?string
    {
        if (count($grantables) < 1) {
            return null;
        }

        $required = implode(', ', $grantables);
        return "any of the following is required ($required)";
    }
}
