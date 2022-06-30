<?php

declare(strict_types=1);

namespace Application\Notification\Handler;

use Application\Notification\Command\SetNotificationReadCommand;
use Application\Notification\Service\NotificationService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SetNotificationReadHandler
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {
    }

    public function __invoke(SetNotificationReadCommand $command): void
    {
        $this->notificationService->read($command->notification);
    }
}
