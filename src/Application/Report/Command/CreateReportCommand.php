<?php

declare(strict_types=1);

namespace Application\Report\Command;

use Doctrine\Common\Collections\Collection;
use Domain\Authentication\Entity\User;
use Domain\Report\ValueObject\Period;

/**
 * Class CreateReportCommand.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class CreateReportCommand
{
    public function __construct(
        public readonly User $employee,
        public Period $period,
        public Collection $managers,
        public ?string $name = null,
        public ?string $description = null,
        public array $documents = [],
    ) {
    }
}
