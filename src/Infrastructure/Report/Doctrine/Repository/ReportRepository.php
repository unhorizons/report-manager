<?php

declare(strict_types=1);

namespace Infrastructure\Report\Doctrine\Repository;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Domain\Authentication\Entity\User;
use Domain\Report\Entity\Report;
use Domain\Report\Exception\DeleteReportWithEvaluationException;
use Domain\Report\Repository\ReportRepositoryInterface;
use Domain\Report\ValueObject\Period;
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
        $result = $this->createQueryBuilder('r')
            ->where('r.status.status = :status')
            ->setParameter('status', (string) Status::unseen())
            ->orderBy('r.created_at', 'DESC')
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
            ->orderBy('r.created_at', 'DESC')
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function hasReportMatchingHashForEmployee(User $employee, string $hash): bool
    {
        try {
            /** @var Report|null $result */
            $result = $this->createQueryBuilder('r')
                ->where('r.period.hash = :hash')
                ->andWhere('r.employee = :employee')
                ->setParameter('hash', $hash)
                ->setParameter('employee', $employee)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            return true;
        }

        return null !== $result;
    }
}
