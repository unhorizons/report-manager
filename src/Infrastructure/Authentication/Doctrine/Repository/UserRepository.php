<?php

/** @noinspection ALL */

declare(strict_types=1);

namespace Infrastructure\Authentication\Doctrine\Repository;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Domain\Authentication\Entity\User;
use Domain\Authentication\Repository\UserRepositoryInterface;
use Infrastructure\Shared\Doctrine\Repository\AbstractRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * Class UserRepository.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class UserRepository extends AbstractRepository implements UserRepositoryInterface, PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findForOauth(string $service, ?string $serviceId, ?string $email): ?User
    {
        if (null === $serviceId || null === $email) {
            return null;
        }

        try {
            /** @var User|null $result */
            $result = $this->createQueryBuilder('u')
                ->where('u.email = :email')
                ->orWhere("u.{$service}_id = :serviceId")
                ->setMaxResults(1)
                ->setParameters([
                    'email' => $email,
                    'serviceId' => $serviceId,
                ])
                ->getQuery()
                ->getOneOrNullResult();

            return $result;
        } catch (NonUniqueResultException) {
            return null;
        }
    }

    public function findOneByEmail(string $email): ?User
    {
        try {
            /** @var User|null $result */
            $result = $this->createQueryBuilder('u')
                ->where('u.email = :email')
                ->setParameter('email', $email)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            return $result;
        } catch (NonUniqueResultException) {
            return null;
        }
    }

    public function findOneByUsername(string $username): ?User
    {
        try {
            /** @var User|null $result */
            $result = $this->createQueryBuilder('u')
                ->where('u.username = :username')
                ->setParameter('username', $username)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            return $result;
        } catch (NonUniqueResultException) {
            return null;
        }
    }

    public function findOneByEmailOrUsername(string $emailOrUsername): ?User
    {
        try {
            /** @var User|null $result */
            $result = $this->createQueryBuilder('u')
                ->where('LOWER(u.email) = :identifier')
                ->orWhere('LOWER(u.username) = :identifier')
                ->setParameter('identifier', mb_strtolower($emailOrUsername))
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            return $result;
        } catch (NonUniqueResultException) {
            return null;
        }
    }

    public function findAllEmployeeWithStats(): array
    {
        /** @var array<array<string, string>> $result */
        $result = $this->findAllEmployeeWithStatsQuery()
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findAllEmployeeWithStatsForManager(User $manager): array
    {
        /** @var array $result */
        $result = $this->findAllEmployeeWithStatsQuery()
            ->leftJoin('r.managers', 't')
            ->andWhere('t = :manager')
            ->setParameter('manager', $manager)
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findAllManager(): array
    {
        /** @var User[] $result */
        $result = $this->findAllManagerQuery()
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findAllManagerQuery(): QueryBuilder
    {
        return  $this->createQueryBuilder('u')
            ->where('u.roles.roles LIKE :role')
            ->setParameter('role', '%ROLE_REPORT_MANAGER%');
    }

    /**
     * {@inheritdoc}
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface|User $user, string $newHashedPassword): void
    {
        if (! $user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->save($user);
    }

    private function findAllEmployeeWithStatsQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('u')
            ->select('u.username', 'u.email', 'u.id', 'u.job_title jobTitle', 'u.last_login_at lastLoginAt', 'COUNT(r.id) reports', 'COUNT(er.id) evaluations')
            ->leftJoin('u.submitted_reports', 'r', 'WITH', 'r.employee = u.id')
            ->leftJoin('u.evaluations', 'e')
            ->leftJoin('e.report', 'er', 'WITH', 'er.employee = u.id')
            ->groupBy('u.id')
            ->orderBy('r.created_at', 'DESC')
            ->where('u.roles.roles = :role')
            ->setParameter('role', '["ROLE_USER"]');
    }
}
