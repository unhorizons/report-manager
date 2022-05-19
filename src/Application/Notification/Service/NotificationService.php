<?php

declare(strict_types=1);

namespace Application\Notification\Service;

use Domain\Authentication\Entity\User;
use Domain\Notification\Repository\NotificationRepositoryInterface;

/**
 * Class NotificationService.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class NotificationService
{
    public function __construct(
        private readonly NotificationRepositoryInterface $repository
    ) {
    }

    public function getChannelsForUser(User $user): array
    {
        return [];
    }
}
