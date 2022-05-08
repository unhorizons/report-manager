<?php

declare(strict_types=1);

namespace Application\Report\Command;

use Domain\Report\Entity\Report;

/**
 * Class SetReportSeenStatusCommand.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class SetReportSeenStatusCommand
{
    public function __construct(
        public readonly Report $report
    ) {
    }
}
