<?php

declare(strict_types=1);

namespace Domain\Report\Repository;

use Domain\Authentication\Entity\User;
use Domain\Report\ValueObject\Period;
use Domain\Shared\Repository\DataRepositoryInterface;

/**
 * Interface ReportRepositoryInterface.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface ReportRepositoryInterface extends DataRepositoryInterface
{
    public function findAllForPeriod(Period $period): array;

    public function findAllForEmployee(User $employee): array;

    public function findAllForEmployeeForPeriod(User $employee, string $source): array;

    public function hasReportMatchingHashForEmployee(User $employee, string $hash, ?string $excludeReportUuid = null): bool;

    public function findAllSeen(): array;

    public function findAllSeenForManager(User $manager): array;

    public function findAllUnseen(): array;

    public function findAllUnseenForManager(User $manager): array;

    public function findAllWithStatus(string $status): array;

    public function findAllWithStatusForManager(string $status, User $manager): array;
}
