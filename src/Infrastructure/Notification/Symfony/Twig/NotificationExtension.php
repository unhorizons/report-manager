<?php

declare(strict_types=1);

namespace Infrastructure\Notification\Symfony\Twig;

use Application\Notification\Service\NotificationService;
use Domain\Authentication\Entity\User;
use Domain\Notification\Entity\Notification;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * NotificationExtension.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class NotificationExtension extends AbstractExtension
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly Security $security
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('recent_notifications', [$this, 'notifications']),
            new TwigFunction('count_notifications', [$this, 'count']),
        ];
    }

    public function notifications(): array
    {
        /** @var User $user */
        $user = $this->security->getUser();

        /** @var Notification[] $notifications */
        $notifications = $this->notificationService->forUser($user);

        return $notifications;
    }

    public function count(): int
    {
        /** @var User $user */
        $user = $this->security->getUser();

        return $this->notificationService->countForUser($user);
    }
}
