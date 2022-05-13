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

    public function hasReportMatchingHashForEmployee(User $employee, string $hash, ?string $excludeReportUuid = null): bool
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
            ->where('r.managers = :manager')
            ->setParameter('manager', $manager)
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findAllSeenForManager(User $manager): array
    {
        /** @var Report[] $result */
        $result = $this->findAllSeenQuery()
            ->where('r.managers = :manager')
            ->setParameter('manager', $manager)
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findAllUnseenForManager(User $manager): array
    {
        /** @var Report[] $result */
        $result = $this->findAllUnseenQuery()
            ->where('r.managers = :manager')
            ->setParameter('manager', $manager)
            ->getQuery()
            ->getResult();

        return $result;
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
}
