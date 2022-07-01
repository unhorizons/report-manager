<?php

declare(strict_types=1);

namespace Application\Notification\Command;

use Domain\Authentication\Entity\User;

/**
 * class SetAllNotificationsReadCommand.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class SetAllNotificationsReadCommand
{
    public function __construct(
        public readonly User $user
    ) {
    }
}
