<?php

declare(strict_types=1);

namespace Infrastructure\Notification\Mercure;

use Domain\Authentication\Entity\User;
use Domain\Notification\Event\NotificationCreatedEvent;
use Domain\Notification\Event\NotificationReadEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

/**
 * Class MercureEventSubscriber.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class MercureEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly HubInterface $hub,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NotificationCreatedEvent::class => 'onNotificationCreated',
            NotificationReadEvent::class => 'onNotificationRead',
        ];
    }

    public function onNotificationCreated(NotificationCreatedEvent $event): void
    {
        $notification = $event->notification;
        $channel = $notification->getChannel();

        if ('public' === $channel && $notification->getUser() instanceof User) {
            $channel = 'user/' . $notification->getUser()->getId();
        }

        $update = new Update(
            topics: "/notifications/{$channel}",
            data: (string) $notification,
            private: true
        );
        $this->hub->publish($update);
    }

    public function onNotificationRead(NotificationReadEvent $event): void
    {
        $user = $event->user;
        $update = new Update(
            topics: "/notifications/user/{$user->getId()}",
            data: '{"type": "mark_as_read"}',
            private: true
        );

        $this->hub->publish($update);
    }
}
