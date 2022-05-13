<?php

declare(strict_types=1);

namespace Infrastructure\Shared\Doctrine\Repository;

/**
 * Trait NativeQueryTrait
 * @package Infrastructure\Shared\Doctrine\Repository
 * @author bernard-ng <bernard@devscast.tech>
 */
trait NativeQueryTrait
{
    public function execute(string $sql, array $data, bool $fetchAll = true): mixed
    {
        try {
            $connection = $this->_em->getConnection();
            $statement = $connection->prepare($sql);
            $result = $statement->executeQuery($data);
            return $fetchAll ? $result->fetchAllAssociative() : $result->fetchAssociative();
        } catch (\Throwable) {
            return [];
        }
    }
}
