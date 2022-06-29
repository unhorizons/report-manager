<?php

declare(strict_types=1);

namespace Domain\Notification\Repository;

use Domain\Authentication\Entity\User;
use Domain\Notification\Entity\Notification;
use Domain\Shared\Repository\CleanableRepositoryInterface;
use Domain\Shared\Repository\DataRepositoryInterface;

/**
 * Interface NotificationRepositoryInterface.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface NotificationRepositoryInterface extends DataRepositoryInterface, CleanableRepositoryInterface
{
    public function saveOrUpdate(Notification $notification): Notification;

    public function findRecentForUser(User $user, array $channels = ['public']): array;

    public function countUnreadForUser(User $user): int;
}
