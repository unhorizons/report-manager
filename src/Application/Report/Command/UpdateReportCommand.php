<?php

declare(strict_types=1);

namespace Application\Report\Command;

use Doctrine\Common\Collections\Collection;
use Domain\Report\Entity\Report;
use Domain\Report\ValueObject\Period;

final class UpdateReportCommand
{
    public function __construct(
        public readonly Report $report,
        public Period $period,
        public Collection $managers,
        public ?string $name = null,
        public ?string $description = null,
        public array $documents = [],
    ) {
        $this->name = $this->report->getName();
        $this->description = $this->report->getDescription();
        $this->period = $this->report->getPeriod();
    }
}
