<?php

declare(strict_types=1);

namespace Domain\Notification\Event;

use Domain\Authentication\Entity\User;

/**
 * Class NotificationReadEvent.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class NotificationReadEvent
{
    public function __construct(
        public readonly User $user
    ) {
    }
}
