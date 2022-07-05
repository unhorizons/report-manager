<?php

declare(strict_types=1);

namespace Domain\Report\Repository;

use Domain\Authentication\Entity\User;
use Domain\Report\ValueObject\Period;
use Domain\Report\ValueObject\Status;
use Domain\Shared\Repository\DataRepositoryInterface;

/**
 * Interface ReportRepositoryInterface.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface ReportRepositoryInterface extends DataRepositoryInterface
{
    public function findAllSeen(): array;

    public function findAllUnseen(): array;

    public function findAllWithStatus(string $status): array;

    public function findAllForPeriod(Period $period): array;

    public function findAllForEmployee(User $employee): array;

    public function findAllForEmployeeForPeriod(User $employee, string $source): array;

    public function findMatchingHashForEmployee(User $employee, string $hash, ?string $excludeReportUuid = null): bool;

    public function findCurrentYearStatsForEmployeeWithStatus(User $employee, Status $status): array;

    public function findCurrentYearStatsForEmployee(User $employee): array;

    public function findStats(): array;

    public function findCurrentYearFrequency(): array;

    public function statusCountForEmployee(User $employee): array;

    public function findAllSeenForManager(User $manager): array;

    public function findAllUnseenForManager(User $manager): array;

    public function findAllForManager(User $manager): array;

    public function findAllWithStatusForManager(string $status, User $manager): array;

    public function searchForManager(User $manager, ?string $query, array $options): array;

    public function findCurrentYearStatsForManager(User $manager): array;

    public function findAllForEmployeeAndManager(User $manager, User $employee): array;

    public function findCurrentYearStatsForManagerWithStatus(User $manager, Status $status): array;

    public function search(?string $query, array $options): array;

    public function findCurrentYearFrequencyForEmployee(User $employee): array;

    public function countUnseenForManager(User $manager): int;
}
