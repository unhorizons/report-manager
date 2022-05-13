<?php

declare(strict_types=1);

namespace Infrastructure\Report\Doctrine\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Domain\Authentication\Entity\User;
use Domain\Report\Entity\Evaluation;
use Domain\Report\Entity\Report;
use Domain\Report\Repository\EvaluationRepositoryInterface;
use Infrastructure\Shared\Doctrine\Repository\AbstractRepository;
use Infrastructure\Shared\Doctrine\Repository\NativeQueryTrait;

/**
 * Class EvaluationRepository.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class EvaluationRepository extends AbstractRepository implements EvaluationRepositoryInterface
{
    use NativeQueryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evaluation::class);
    }

    public function addEvaluationToReport(Evaluation $evaluation, Report $report): void
    {
        $evaluation->setReport($report);
        $this->save($report);
    }

    public function findAllEvaluationForReport(Report $report): array
    {
        /** @var Report[] $result */
        $result = $this->createQueryBuilder('e')
            ->where('e.report = :report')
            ->setParameter('report', $report)
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findAllEvaluationForEmployee(User $employee): array
    {
        /** @var Report[] $result */
        $result = $this->createQueryBuilder('e')
            ->select('e AS evaluation', 'report.uuid AS report_uuid')
            ->leftJoin('e.report', 'report')
            ->leftJoin('report.employee', 'employee')
            ->where('employee = :employee')
            ->setParameter('employee', $employee)
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findCurrentYearEvaluationStatsForManager(User $manager): array
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
            FROM evaluation
            WHERE (created_at BETWEEN :start AND :end) AND manager_id = :manager
        SQL;

        return $this->execute($sql, [
            'end' => $end,
            'start' => $start,
            'manager' => $manager->getId()
        ], false);
    }
}
