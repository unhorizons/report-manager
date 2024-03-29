<?php

declare(strict_types=1);

namespace Domain\Authentication\Repository;

use Domain\Authentication\Entity\User;
use Domain\Shared\Repository\DataRepositoryInterface;

/**
 * Interface UserRepository.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
interface UserRepositoryInterface extends DataRepositoryInterface
{
    public function findForOauth(string $service, ?string $serviceId, ?string $email): ?User;

    public function findOneByEmail(string $email): ?User;

    public function findOneByUsername(string $username): ?User;

    public function findOneByEmailOrUsername(string $emailOrUsername): ?User;

    public function upgradePassword(User $user, string $newHashedPassword): void;

    public function findAllEmployeeWithStats(): array;

    public function findAllEmployeeWithStatsForManager(User $manager): array;

    public function findAllManager(): array;
}
