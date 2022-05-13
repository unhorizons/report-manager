<?php

declare(strict_types=1);

namespace Application\Report\Command;

use Domain\Authentication\Entity\User;
use Domain\Report\ValueObject\Period;

/**
 * Class SearchReportCommand.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class SearchReportCommand
{
    public function __construct(
        public readonly User $user,
        public Period $period,
        public ?string $query = null,
        public bool $use_period = false,
        public bool $seen = true,
        public bool $unseen = true
    ) {
    }
}
