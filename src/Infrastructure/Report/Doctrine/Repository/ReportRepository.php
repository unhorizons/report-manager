<?php

declare(strict_types=1);

namespace Infrastructure\Report\Doctrine\Repository;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Domain\Authentication\Entity\User;
use Domain\Report\Entity\Report;
use Domain\Report\Exception\DeleteReportWithEvaluationException;
use Domain\Report\Repository\ReportRepositoryInterface;
use Domain\Report\ValueObject\Period;
use Domain\Report\ValueObject\Status;
use Infrastructure\Shared\Doctrine\Repository\AbstractRepository;
use Infrastructure\Shared\Doctrine\Repository\NativeQueryTrait;

/**
 * Class ReportRepository.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class ReportRepository extends AbstractRepository implements ReportRepositoryInterface
{
    use NativeQueryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

    public function delete(object $entity): void
    {
        try {
            parent::delete($entity);
        } catch (ForeignKeyConstraintViolationException $e) {
            throw new DeleteReportWithEvaluationException(previous: $e);
        }
    }

    public function findAllForPeriod(Period $period): array
    {
        /** @var Report[] $result */
        $result = $this->createQueryBuilder('r')
            ->where('r.period.starting_at = :start')
            ->andWhere('r.period.ending_at = :end')
            ->setParameter('start', $period->getStartingAt())
            ->setParameter('end', $period->getEndingAt())
            ->orderBy('r.created_at', 'DESC')
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findAllForEmployee(User $employee): array
    {
        /** @var Report[] $result */
        $result = $this->createQueryBuilder('r')
            ->where('r.employee = :employee')
            ->setParameter('employee', $employee)
            ->orderBy('r.created_at', 'DESC')
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findAllForEmployeeForPeriod(User $employee, string $source): array
    {
        /** @var Report[] $result */
        $result = $this->createQueryBuilder('r')
            ->where('r.period.source = :source')
            ->andWhere('r.employee = :employee')
            ->setParameter('employee', $employee)
            ->setParameter('source', $source)
            ->orderBy('r.created_at', 'DESC')
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findAllUnseen(): array
    {
        /** @var Report[] $result */
        $result = $this->findAllUnseenQuery()
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findAllSeen(): array
    {
        /** @var Report[] $result */
        $result = $this->findAllSeenQuery()
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findMatchingHashForEmployee(User $employee, string $hash, ?string $excludeReportUuid = null): bool
    {
        try {
            $query = $this->createQueryBuilder('r')
                ->where('r.period.hash = :hash')
                ->andWhere('r.employee = :employee')
                ->setParameter('hash', $hash)
                ->setParameter('employee', $employee);

            if (null !== $excludeReportUuid) {
                $query->andWhere('r.uuid <> :uuid')
                    ->setParameter('uuid', $excludeReportUuid);
            }

            return null !== $query
                    ->getQuery()
                    ->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            return false;
        }
    }

    public function findAllWithStatus(string $status): array
    {
        return match ($status) {
            'seen' => $this->findAllSeen(),
            'unseen' => $this->findAllUnseen(),
            default => $this->findAll()
        };
    }

    public function findAllWithStatusForManager(string $status, User $manager): array
    {
        return match ($status) {
            'seen' => $this->findAllSeenForManager($manager),
            'unseen' => $this->findAllUnseenForManager($manager),
            default => $this->findAllForManager($manager)
        };
    }

    public function findAllForManager(User $manager): array
    {
        /** @var Report[] $result */
        $result = $this->createQueryBuilder('r')
            ->leftJoin('r.managers', 't')
            ->where('t = :manager')
            ->setParameter('manager', $manager)
            ->orderBy('r.created_at', 'DESC')
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findAllSeenForManager(User $manager): array
    {
        /** @var Report[] $result */
        $result = $this->findAllSeenQuery()
            ->leftJoin('r.managers', 't')
            ->andWhere('t = :manager')
            ->setParameter('manager', $manager)
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findAllUnseenForManager(User $manager): array
    {
        /** @var Report[] $result */
        $result = $this->findAllUnseenQuery()
            ->leftJoin('r.managers', 't')
            ->andWhere('t = :manager')
            ->setParameter('manager', $manager)
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function statusCountForEmployee(User $employee): array
    {
        $sql = <<< SQL
            SELECT
                (SELECT COUNT(id) FROM  report WHERE status = :seen AND employee_id = :employee) AS seen, 
                (SELECT COUNT(id) FROM report WHERE status = :unseen AND employee_id = :employee) AS unseen
            FROM dual;
        SQL;

        return $this->execute($sql, [
            'seen' => 'seen',
            'unseen' => 'unseen',
            'employee' => $employee->getId()
        ], false);
    }

    public function findCurrentYearStatsForEmployee(User $employee): array
    {
        $sql = <<< SQL
            SELECT
                (SELECT COUNT(id) FROM  report WHERE employee_id = :employee) AS reports, 
                (
                    SELECT COUNT(evaluation.id) FROM evaluation 
                    LEFT JOIN report ON report.id = evaluation.report_id 
                    WHERE report.employee_id = :employee
                ) AS evaluations
            FROM dual;
        SQL;

        return $this->execute($sql, [
            'employee' => $employee->getId()
        ], false);
    }

    public function findCurrentYearStatsForEmployeeWithStatus(User $employee, Status $status): array
    {
        $start = (new \DateTimeImmutable('first day of January this year'))->format('Y-m-d');
        $end = (new \DateTimeImmutable('last day of December this year'))->format('Y-m-d');

        $sql = <<< SQL
            SELECT 
                SUM(MONTH(created_at) = 1) AS 'Jan',
                SUM(MONTH(created_at) = 2) AS 'Feb',
                SUM(MONTH(created_at) = 3) AS 'Mar',
                SUM(MONTH(created_at) = 4) AS 'Apr',
                SUM(MONTH(created_at) = 5) AS 'May',
                SUM(MONTH(created_at) = 6) AS 'Jun',
                SUM(MONTH(created_at) = 7) AS 'Jul',
                SUM(MONTH(created_at) = 8) AS 'Aug',
                SUM(MONTH(created_at) = 9) AS 'Sep',
                SUM(MONTH(created_at) = 10) AS 'Oct',
                SUM(MONTH(created_at) = 11) AS 'Nov',
                SUM(MONTH(created_at) = 12) AS 'Dec'
            FROM report
            WHERE (created_at BETWEEN :start AND :end) AND (status = :status AND employee_id = :employee)
        SQL;

        return $this->execute($sql, [
            'end' => $end,
            'start' => $start,
            'status' => (string) $status,
            'employee' => $employee->getId()
        ], false);
    }

    public function findCurrentYearStatsForManagerWithStatus(User $manager, Status $status): array
    {
        $start = (new \DateTimeImmutable('first day of January this year'))->format('Y-m-d');
        $end = (new \DateTimeImmutable('last day of December this year'))->format('Y-m-d');

        $sql = <<< SQL
            SELECT 
                SUM(MONTH(created_at) = 1) AS 'Jan',
                SUM(MONTH(created_at) = 2) AS 'Feb',
                SUM(MONTH(created_at) = 3) AS 'Mar',
                SUM(MONTH(created_at) = 4) AS 'Apr',
                SUM(MONTH(created_at) = 5) AS 'May',
                SUM(MONTH(created_at) = 6) AS 'Jun',
                SUM(MONTH(created_at) = 7) AS 'Jul',
                SUM(MONTH(created_at) = 8) AS 'Aug',
                SUM(MONTH(created_at) = 9) AS 'Sep',
                SUM(MONTH(created_at) = 10) AS 'Oct',
                SUM(MONTH(created_at) = 11) AS 'Nov',
                SUM(MONTH(created_at) = 12) AS 'Dec'
            FROM report
            LEFT JOIN manager_assigned_report ON report.id = manager_assigned_report.report_id
            WHERE (created_at BETWEEN :start AND :end) AND (status = :status AND manager_assigned_report.manager_id = :manager)
        SQL;

        return $this->execute($sql, [
            'end' => $end,
            'start' => $start,
            'status' => (string) $status,
            'manager' => $manager->getId()
        ], false);
    }

    public function searchForManager(User $manager, string $query): array
    {
        /** @var Report[] $result */
        $result = $this->createQueryBuilder('r')
            ->leftJoin('r.managers', 'm')
            ->where('r.name LIKE :query')
            ->andWhere('m = :manager')
            ->setParameter('query', mb_strtolower($query))
            ->setParameter('manager', $manager)
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findCurrentYearStatsForManager(User $manager): array
    {
        $sql = <<< SQL
            SELECT
                (
                    SELECT COUNT(id) FROM  report
                    LEFT JOIN manager_assigned_report ON report.id = manager_assigned_report.report_id
                    WHERE manager_assigned_report.manager_id = :manager
                ) AS reports,
                (
                    SELECT COUNT(evaluation.id) FROM evaluation 
                    WHERE manager_id = :manager
                ) AS evaluations
            FROM dual;
        SQL;

        return $this->execute($sql, [
            'manager' => $manager->getId()
        ], false);
    }

    public function findAllForEmployeeAndManager(User $manager, User $employee): array
    {
        /** @var array $result */
        $result = $this->createQueryBuilder('r')
            ->leftJoin('r.managers', 'm')
            ->where('r.employee = :employee')
            ->andWhere('m = :manager')
            ->setParameter('manager', $manager)
            ->setParameter('employee', $employee)
            ->getQuery()
            ->getResult();

        return $result;
    }

    private function findAllUnseenQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->where('r.status.status = :status')
            ->setParameter('status', (string)Status::unseen())
            ->orderBy('r.created_at', 'DESC');
    }

    private function findAllSeenQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->where('r.status.status = :status')
            ->setParameter('status', (string)Status::seen())
            ->orderBy('r.created_at', 'DESC');
    }
}
