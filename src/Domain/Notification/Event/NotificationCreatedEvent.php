<?php

declare(strict_types=1);

namespace Domain\Notification\Event;

use Domain\Notification\Entity\Notification;

/**
 * Class NotificationCreatedEvent.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class NotificationCreatedEvent
{
    public function __construct(
        public readonly Notification $notification
    ) {
    }
}
