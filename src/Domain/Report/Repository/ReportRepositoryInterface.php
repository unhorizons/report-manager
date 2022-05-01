<?php

declare(strict_types=1);

namespace Domain\Report\Repository;

use Domain\Authentication\Entity\User;
use Domain\Report\Entity\Report;
use Domain\Report\ValueObject\IntervalDate;
use Domain\Shared\Repository\DataRepositoryInterface;

/**
 * Interface ReportRepositoryInterface.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface ReportRepositoryInterface extends DataRepositoryInterface
{
    public function findAllForInterval(IntervalDate $interval): ?Report;

    public function findAllForUser(User $user): array;

    public function findAllForUserInInterval(User $user, IntervalDate $interval): array;

    public function findAllSeen(): array;

    public function findAllUnseen(): array;
}
