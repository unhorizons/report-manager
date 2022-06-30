<?php

declare(strict_types=1);

namespace Application\Notification\Command;

use Domain\Notification\Entity\Notification;

/**
 * class SetNotificationReadCommand.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class SetNotificationReadCommand
{
    public function __construct(
        public readonly Notification $notification
    ) {
    }
}
