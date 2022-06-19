<?php

declare(strict_types=1);

namespace Domain\Report\Event;

use Domain\Report\Entity\Report;

/**
 * Class ReportCreatedEvent.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class ReportCreatedEvent
{
    public function __construct(
        public readonly Report $report
    ) {
    }
}
