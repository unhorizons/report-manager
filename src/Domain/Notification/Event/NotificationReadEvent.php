<?php

declare(strict_types=1);

namespace Domain\Notification\Event;

use Domain\Notification\Entity\Notification;

/**
 * Class NotificationReadEvent
 * @package Domain\Notification\Event
 * @author bernard-ng <bernard@devscast.tech>
 */
final class NotificationReadEvent
{
    private function __construct(
        public readonly Notification $notification
    ) {
    }
}
