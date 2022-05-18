<?php

declare(strict_types=1);

namespace Domain\Shared\Repository;

/**
 * Interface CleanableRepositoryInterface
 * @package Domain\Shared\Repository
 * @author bernard-ng <bernard@devscast.tech>
 */
interface CleanableRepositoryInterface
{
    public function clean(): int;
}
