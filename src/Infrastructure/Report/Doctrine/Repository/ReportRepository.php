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
            'employee' => $employee->getId(),
        ], false);
    }

    public function findCurrentYearStatsForEmployee(User $employee): array
    {
        return $this->findCurrentYearStatsForUser($employee);
    }

    public function findCurrentYearStatsForEmployeeWithStatus(User $employee, Status $status): array
    {
        $interval = $this->createDateTimeInterval('first day of January this year', 'last day of December this year');

        $sql = <<< SQL
            SELECT {$this->createMonthSumSQL('created_at')} FROM report
            WHERE (created_at BETWEEN :start AND :end) AND (status = :status AND employee_id = :employee)
        SQL;

        return $this->execute($sql, [
            'end' => $interval[1],
            'start' => $interval[0],
            'status' => (string) $status,
            'employee' => $employee->getId(),
        ], false);
    }

    public function findCurrentYearStatsForManagerWithStatus(User $manager, Status $status): array
    {
        $interval = $this->createDateTimeInterval('first day of January this year', 'last day of December this year');

        $sql = <<< SQL
            SELECT {$this->createMonthSumSQL('created_at')} FROM report
            LEFT JOIN manager_assigned_report ON report.id = manager_assigned_report.report_id
            WHERE (created_at BETWEEN :start AND :end) AND (status = :status AND manager_assigned_report.manager_id = :manager)
        SQL;

        return $this->execute($sql, [
            'end' => $interval[1],
            'start' => $interval[0],
            'status' => (string) $status,
            'manager' => $manager->getId(),
        ], false);
    }

    public function searchForManager(User $manager, ?string $query, array $options): array
    {
        $qb = $this->searchQuery($query, $options)
            ->andWhere('m = :manager')
            ->setParameter('manager', $manager);

        /** @var Report[] $result */
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function findCurrentYearStatsForManager(User $manager): array
    {
        return $this->findCurrentYearStatsForUser($manager);
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

    public function search(?string $query, array $options): array
    {
        /** @var Report[] $result */
        $result = $this->searchQuery($query, $options)
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findCurrentYearFrequencyForEmployee(User $employee): array
    {
        $interval = $this->createDateTimeInterval('first day of January this year', 'last day of December this year');

        $sql = <<< SQL
            SELECT {$this->createMonthSumSQL('created_at')} FROM report
            WHERE (created_at BETWEEN :start AND :end) AND employee_id = :employee
        SQL;

        return $this->execute($sql, [
            'end' => $interval[0],
            'start' => $interval[1],
            'employee' => $employee->getId(),
        ], false);
    }

    private function findAllUnseenQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->where('r.status.status = :status')
            ->setParameter('status', (string) Status::unseen())
            ->orderBy('r.created_at', 'DESC');
    }

    private function findAllSeenQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->where('r.status.status = :status')
            ->setParameter('status', (string) Status::seen())
            ->orderBy('r.created_at', 'DESC');
    }

    private function searchQuery(?string $query, array $options): QueryBuilder
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.managers', 'm');

        if (! empty($query)) {
            $qb->andWhere('CONCAT(r.name, r.description) LIKE :query')
                ->setParameter('query', mb_strtolower("%{$query}%"));
        }

        if ($options['seen'] && ! $options['unseen']) {
            $qb->andWhere('r.status.status = :status')->setParameter('status', 'seen');
        } elseif ($options['unseen'] && ! $options['seen']) {
            $qb->andWhere('r.status.status = :status')->setParameter('status', 'unseen');
        }

        if (true === $options['use_period']) {
            /** @var Period $period */
            $period = $options['period'];

            $qb
                ->andWhere('r.period.starting_at BETWEEN :start AND :end')
                ->andWhere('r.period.ending_at BETWEEN :start AND :end')
                ->setParameter('start', $period->getStartingAt())
                ->setParameter('end', $period->getEndingAt());
        }

        return $qb;
    }

    private function calculateProgressionRatio(int $previous, int $current): int|float
    {
        return 0 === $previous ?
            $current * 100 :
            round(($current - $previous) * ($previous / 100), 2);
    }

    private function createDateTimeInterval(string $start, string $end): array
    {
        return [
            (new \DateTimeImmutable($start))->format('Y-m-d'),
            (new \DateTimeImmutable($end))->format('Y-m-d'),
        ];
    }

    private function createCurrentYearStatsSQL(User $user): string
    {
        if ($user->hasRole('ROLE_REPORT_MANAGER')) {
            return <<< SQL
                SELECT
                    (
                        SELECT COUNT(id) FROM report 
                        LEFT JOIN manager_assigned_report on report.id = manager_assigned_report.report_id
                        WHERE manager_assigned_report.manager_id = :user
                    ) AS reports,
                    (
                        SELECT COUNT(id) FROM report 
                        LEFT JOIN manager_assigned_report on report.id = manager_assigned_report.report_id
                        WHERE manager_assigned_report.manager_id = :user AND created_at BETWEEN :previous_month_start AND :previous_month_end
                    ) AS reports_previous_month,
                    (
                        SELECT COUNT(id) FROM report 
                        LEFT JOIN manager_assigned_report on report.id = manager_assigned_report.report_id
                        WHERE manager_assigned_report.manager_id = :user AND created_at BETWEEN :current_month_start AND :current_month_end
                    ) AS reports_current_month,
                    (
                        SELECT COUNT(id) FROM evaluation WHERE manager_id = :user
                     ) AS evaluations,
                     (
                        SELECT COUNT(id) FROM evaluation 
                        WHERE manager_id = :user AND created_at BETWEEN :previous_month_start AND :previous_month_end
                    ) AS evaluations_previous_month,
                     (
                        SELECT COUNT(evaluation.id) FROM evaluation 
                        WHERE manager_id = :user AND created_at BETWEEN :current_month_start AND :current_month_end
                    ) AS evaluations_current_month
                FROM dual;
            SQL;
        }

        return <<< SQL
                SELECT
                    (SELECT COUNT(id) FROM report WHERE employee_id = :user) AS reports,
                    (SELECT COUNT(id) FROM report WHERE employee_id = :user AND created_at BETWEEN :previous_month_start AND :previous_month_end) AS reports_previous_month,
                    (SELECT COUNT(id) FROM report WHERE employee_id = :user AND created_at BETWEEN :current_month_start AND :current_month_end) AS reports_current_month,
                    (
                        SELECT COUNT(evaluation.id) FROM evaluation 
                        LEFT JOIN report ON report.id = evaluation.report_id 
                        WHERE report.employee_id = :user
                    ) AS evaluations,
                     (
                        SELECT COUNT(evaluation.id) FROM evaluation 
                        LEFT JOIN report ON report.id = evaluation.report_id 
                        WHERE report.employee_id = :user AND evaluation.created_at BETWEEN :previous_month_start AND :previous_month_end
                    ) AS evaluations_previous_month,
                     (
                        SELECT COUNT(evaluation.id) FROM evaluation 
                        LEFT JOIN report ON report.id = evaluation.report_id 
                        WHERE report.employee_id = :user AND evaluation.created_at BETWEEN :current_month_start AND :current_month_end
                    ) AS evaluations_current_month
                FROM dual;
            SQL;
    }

    private function findCurrentYearStatsForUser(User $user): array
    {
        $previousMonth = $this->createDateTimeInterval('first day of previous month', 'last day of previous month');
        $currentMonth = $this->createDateTimeInterval('first day of this month', 'last day of this month');
        $data = $this->execute($this->createCurrentYearStatsSQL($user), [
            'user' => $user->getId(),
            'previous_month_start' => $previousMonth[0],
            'previous_month_end' => $previousMonth[1],
            'current_month_start' => $currentMonth[0],
            'current_month_end' => $currentMonth[1],
        ], false);

        $reportMonthRatio = $this->calculateProgressionRatio($data['reports_previous_month'], $data['reports_current_month']);
        $evaluationMonthRatio = $this->calculateProgressionRatio($data['evaluations_previous_month'], $data['evaluations_current_month']);

        return [
            'reports_month_ratio' => $reportMonthRatio,
            'evaluations_month_ratio' => $evaluationMonthRatio,
            'reports' => $data['reports'],
            'evaluations' => $data['evaluations'],
        ];
    }

    private function createMonthSumSQL(string $date): string
    {
        return <<< SQL
            SUM(MONTH({$date}) = 1) AS 'Jan',
            SUM(MONTH({$date}) = 2) AS 'Feb',
            SUM(MONTH({$date}) = 3) AS 'Mar',
            SUM(MONTH({$date}) = 4) AS 'Apr',
            SUM(MONTH({$date}) = 5) AS 'May',
            SUM(MONTH({$date}) = 6) AS 'Jun',
            SUM(MONTH({$date}) = 7) AS 'Jul',
            SUM(MONTH({$date}) = 8) AS 'Aug',
            SUM(MONTH({$date}) = 9) AS 'Sep',
            SUM(MONTH({$date}) = 10) AS 'Oct',
            SUM(MONTH({$date}) = 11) AS 'Nov',
            SUM(MONTH({$date}) = 12) AS 'Dec'
        SQL;
    }
}
