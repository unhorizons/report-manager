<?php

declare(strict_types=1);

namespace Infrastructure\Notification\Symfony\EventSubscriber;

use Application\Notification\Service\NotificationService;
use Domain\Authentication\Entity\User;
use Domain\Report\Event\ReportCreatedEvent;
use Domain\Report\Event\ReportSeenEvent;
use Domain\Report\Event\ReportUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ReportEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ReportCreatedEvent::class => 'onReportCreated',
            ReportUpdatedEvent::class => 'onReportUpdated',
            ReportSeenEvent::class => 'onReportSeen',
        ];
    }

    public function onReportCreated(ReportCreatedEvent $event): void
    {
        /** @var User $manager */
        foreach ($event->report->getManagers() as $manager) {
            $this->notificationService->notifyUser(
                user: $manager,
                message: sprintf(
                    'Vous avez reçu un nouveau rapport "%s" de %s',
                    $event->report->getName(),
                    $event->report->getEmployee()?->getUsername()
                ),
                entity: $event->report
            );
        }
    }

    public function onReportSeen(ReportSeenEvent $event): void
    {
        /** @var User $employee */
        $employee = $event->report->getEmployee();
        $this->notificationService->notifyUser(
            user: $employee,
            message: sprintf('Votre rapport "%s" a été lu', $event->report->getName()),
            entity: $event->report
        );
    }

    public function onReportUpdated(ReportUpdatedEvent $event): void
    {
        /** @var User $manager */
        foreach ($event->report->getManagers() as $manager) {
            $this->notificationService->notifyUser(
                user: $manager,
                message: sprintf(
                    'le rapport "%s" de %s a été mise à jour',
                    $event->report->getName(),
                    $event->report->getEmployee()?->getUsername()
                ),
                entity: $event->report
            );
        }
    }
}
