<?php

declare(strict_types=1);

namespace Infrastructure\Report\Doctrine\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Domain\Authentication\Entity\User;
use Domain\Report\Entity\Evaluation;
use Domain\Report\Entity\Report;
use Domain\Report\Repository\EvaluationRepositoryInterface;
use Infrastructure\Shared\Doctrine\Repository\AbstractRepository;

/**
 * Class EvaluationRepository.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class EvaluationRepository extends AbstractRepository implements EvaluationRepositoryInterface
{
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
}
