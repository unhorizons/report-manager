<?php

declare(strict_types=1);

namespace Infrastructure\Report\Doctrine\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Domain\Authentication\Entity\User;
use Domain\Report\Entity\Report;
use Domain\Report\Repository\ReportRepositoryInterface;
use Domain\Report\ValueObject\IntervalDate;
use Domain\Report\ValueObject\Status;
use Infrastructure\Shared\Doctrine\Repository\AbstractRepository;

/**
 * Class ReportRepository.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class ReportRepository extends AbstractRepository implements ReportRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

    public function findAllForInterval(IntervalDate $interval): ?Report
    {
        /** @var Report|null $result */
        $result = $this->createQueryBuilder('r')
            ->where('r.interval_date.starting_at = :start')
            ->andWhere('r.interval_date.ending_at = :end')
            ->setParameter('start', $interval->getStartingAt())
            ->set('end', $interval->getEndingAt());

        return $result;
    }

    public function findAllForEmployee(User $employee): array
    {
        /** @var Report[] $result */
        $result = $this->createQueryBuilder('r')
            ->where('r.employee = :employee')
            ->setParameter('employee', $employee)
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findAllForEmployeeInInterval(User $employee, IntervalDate $interval): array
    {
        /** @var Report[] $result */
        $result = $this->createQueryBuilder('r')
            ->where('r.interval_date.starting_at = :start')
            ->andWhere('r.interval_date.ending_at = :end')
            ->andWhere('r.employee = :employee')
            ->setParameter('start', $interval->getStartingAt())
            ->setParameter('end', $interval->getEndingAt())
            ->set('employee', $employee)
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findAllUnseen(): array
    {
        /** @var Report[] $result */
        $result = $this->createQueryBuilder('r')
            ->where('r.status.status = :status')
            ->setParameter('status', (string) Status::unseen())
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findAllSeen(): array
    {
        /** @var Report[] $result */
        $result = $this->createQueryBuilder('r')
            ->where('r.status.status = :status')
            ->setParameter('status', (string) Status::seen())
            ->getQuery()
            ->getResult();

        return $result;
    }
}
