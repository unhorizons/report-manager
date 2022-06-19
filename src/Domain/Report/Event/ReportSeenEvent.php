<?php

declare(strict_types=1);

namespace Domain\Report\Event;

use Domain\Report\Entity\Report;

/**
 * Class ReportSeenEvent.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class ReportSeenEvent
{
    public function __construct(
        public readonly Report $report
    ) {
    }
}
