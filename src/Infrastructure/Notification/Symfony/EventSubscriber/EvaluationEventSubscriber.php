<?php

declare(strict_types=1);

namespace Infrastructure\Notification\Symfony\EventSubscriber;

use Application\Notification\Service\NotificationService;
use Domain\Authentication\Entity\User;
use Domain\Report\Event\EvaluationCreatedEvent;
use Domain\Report\Event\EvaluationUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class EvaluationEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EvaluationCreatedEvent::class => 'onEvaluationCreated',
            EvaluationUpdatedEvent::class => 'onEvaluationUpdated',
        ];
    }

    public function onEvaluationCreated(EvaluationCreatedEvent $event): void
    {
        /** @var User $employee */
        $employee = $event->evaluation->getReport()?->getEmployee();
        $message = sprintf(
            'Vous avez reçu une nouvelle évaluation de la part de %s',
            $event->evaluation->getManager()?->getUsername()
        );

        $this->notificationService->notifyUser(
            user: $employee,
            message: $message,
            entity: $event->evaluation
        );
    }

    public function onEvaluationUpdated(EvaluationUpdatedEvent $event): void
    {
        /** @var User $employee */
        $employee = $event->evaluation->getReport()?->getEmployee();
        $message = sprintf(
            "l'évaluation de %s a été mise à jour",
            $event->evaluation->getManager()?->getUsername()
        );

        $this->notificationService->notifyUser(
            user: $employee,
            message: $message,
            entity: $event->evaluation
        );
    }
}
