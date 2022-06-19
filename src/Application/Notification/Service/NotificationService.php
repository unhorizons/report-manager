<?php

declare(strict_types=1);

namespace Application\Notification\Service;

use Domain\Authentication\Entity\User;
use Domain\Authentication\Repository\UserRepositoryInterface;
use Domain\Notification\Entity\Notification;
use Domain\Notification\Event\NotificationCreatedEvent;
use Domain\Notification\Event\NotificationReadEvent;
use Domain\Notification\Repository\NotificationRepositoryInterface;
use Infrastructure\Notification\Symfony\Encoder\PathEncoder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class NotificationService.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class NotificationService
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly NotificationRepositoryInterface $notificationRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly Security $security
    ) {
    }

    public function notifyChannel(string $channel, string $message, ?object $entity = null): Notification
    {
        /** @var string|null $url */
        $url = $entity ? $this->serializer->serialize($entity, PathEncoder::FORMAT) : null;
        $notification = (new Notification())
            ->setMessage($message)
            ->setUrl($url)
            ->setTarget($entity ? $this->getHashForEntity($entity) : null)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setChannel($channel);

        $this->notificationRepository->save($notification);
        $this->dispatcher->dispatch(new NotificationCreatedEvent($notification));

        return $notification;
    }

    public function notifyUser(User $user, string $message, object $entity): Notification
    {
        $url = $this->serializer->serialize($entity, PathEncoder::FORMAT);
        $notification = (new Notification())
            ->setMessage($message)
            ->setUrl($url)
            ->setTarget($this->getHashForEntity($entity))
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUser($user);

        $this->notificationRepository->saveOrUpdate($notification);
        $this->dispatcher->dispatch(new NotificationCreatedEvent($notification));

        return $notification;
    }

    public function forUser(User $user): array
    {
        return $this->notificationRepository->findRecentForUser(
            user: $user,
            channels: $this->getChannelsForUser($user)
        );
    }

    public function readAll(User $user): void
    {
        $user->setNotificationsReadAt(new \DateTimeImmutable());
        $this->userRepository->save($user);
        $this->dispatcher->dispatch(new NotificationReadEvent($user));
    }

    public function getChannelsForUser(User $user): array
    {
        $channels = [
            sprintf('user/%s', $user->getId()),
            'public',
        ];

        if ($this->security->isGranted('ROLE_ADMIN', $user)) {
            $channels[] = 'admin';
        }

        return $channels;
    }

    /**
     * Extrait un hash pour une notification className::id.
     */
    private function getHashForEntity(object $entity): string
    {
        $hash = $entity::class;
        if (method_exists($entity, 'getId')) {
            $hash .= sprintf('::%s', (string) $entity->getId());
        }

        return $hash;
    }
}
