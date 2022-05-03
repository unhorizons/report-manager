<?php

declare(strict_types=1);

namespace Application\Report\Command;

use Domain\Report\Entity\Report;

/**
 * Class DeleteReportCommand.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class DeleteReportCommand
{
    public function __construct(
        public readonly Report $report
    ) {
    }
}
