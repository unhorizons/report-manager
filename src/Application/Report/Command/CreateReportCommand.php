<?php

declare(strict_types=1);

namespace Application\Report\Command;

use Domain\Authentication\Entity\User;
use Domain\Report\ValueObject\IntervalDate;

/**
 * Class CreateReportCommand.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class CreateReportCommand
{
    public function __construct(
        public readonly User $employee,
        public ?string $name = null,
        public ?string $description = null,
        public ?object $document_file = null,
        public ?IntervalDate $interval_date = null,
    ) {
        $this->interval_date = IntervalDate::createDefault();
    }
}
