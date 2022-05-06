<?php

declare(strict_types=1);

namespace Application\Report\Command;

use Domain\Report\Entity\Report;
use Domain\Report\ValueObject\Period;

final class UpdateReportCommand
{
    public function __construct(
        public readonly Report $report,
        public ?string $name = null,
        public ?string $description = null,
        public array $documents = [],
        public ?Period $period = null,
    ) {
    }
}
