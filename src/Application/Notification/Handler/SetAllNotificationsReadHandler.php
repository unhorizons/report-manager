<?php

declare(strict_types=1);

namespace Application\Notification\Handler;

use Application\Notification\Command\SetAllNotificationsReadCommand;
use Application\Notification\Service\NotificationService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SetAllNotificationsReadHandler
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {
    }

    public function __invoke(SetAllNotificationsReadCommand $command): void
    {
        $this->notificationService->readAll($command->user);
    }
}
