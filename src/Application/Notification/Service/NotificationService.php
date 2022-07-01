<?php

declare(strict_types=1);

namespace Application\Notification\Service;

use Domain\Authentication\Entity\User;
use Domain\Authentication\Repository\UserRepositoryInterface;
use Domain\Notification\Entity\Notification;
use Domain\Notification\Repository\NotificationRepositoryInterface;
use Domain\Report\Entity\Evaluation;
use Domain\Report\Entity\Report;
use Infrastructure\Notification\WebPushService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class NotificationService.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class NotificationService
{
    public function __construct(
        private readonly NotificationRepositoryInterface $notificationRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly Security $security,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly WebPushService $pushService
    ) {
    }

    public function notifyChannel(string $channel, string $message, ?object $entity = null): Notification
    {
        /** @var string|null $url */
        $url = $entity ? $this->getUrlForEntityChannel($entity) : null;
        $notification = (new Notification())
            ->setMessage($message)
            ->setUrl($url)
            ->setTarget($entity ? $this->getHashForEntity($entity) : null)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setChannel($channel);

        $this->notificationRepository->save($notification);

        $this->pushService->notifyChannel($notification);
        //$this->dispatcher->dispatch(new NotificationCreatedEvent($notification));

        return $notification;
    }

    public function notifyUser(User $user, string $message, object $entity): Notification
    {
        $url = $this->getUrlForEntityUser($entity, $user);
        $notification = (new Notification())
            ->setMessage($message)
            ->setUrl($url)
            ->setTarget($this->getHashForEntity($entity))
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUser($user);

        $this->notificationRepository->saveOrUpdate($notification);
        $this->pushService->notifyUser($notification, $user);
        //$this->dispatcher->dispatch(new NotificationCreatedEvent($notification));

        return $notification;
    }

    public function forUser(User $user): array
    {
        return $this->notificationRepository->findRecentForUser(
            user: $user,
            channels: $this->getChannelsForUser($user)
        );
    }

    public function countForUser(User $user): int
    {
        return $this->notificationRepository->countUnreadForUser($user);
    }

    public function readAll(User $user): void
    {
        $user->setNotificationsReadAt(new \DateTimeImmutable());
        $this->notificationRepository->setAllReadForUser($user);
        $this->userRepository->save($user);
        //$this->dispatcher->dispatch(new NotificationReadEvent($user));
    }

    public function read(Notification $notification): void
    {
        $notification->setIsRead(true);
        $this->notificationRepository->save($notification);
        //$this->dispatcher->dispatch(new NotificationReadEvent($user));
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

    private function getUrlForEntityChannel(object $entity): string
    {
        return '';
    }

    private function getUrlForEntityUser(object $entity, User $user): string
    {
        $route = $this->security->isGranted('ROLE_REPORT_MANAGER', $user) ?
            'report_employee_report_show' :
            'report_manager_report_show';
        $parameters = match (true) {
            $entity instanceof Evaluation => [
                'uuid' => $entity->getReport()?->getUuid(),
            ],
            $entity instanceof Report => [
                'uuid' => $entity->getUuid(),
            ],
            default => []
        };

        return $this->urlGenerator->generate($route, $parameters);
    }
}
