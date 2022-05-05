<?php

declare(strict_types=1);

namespace Infrastructure\Report\Doctrine\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Domain\Report\Entity\Document;
use Domain\Report\Repository\DocumentRepositoryInterface;
use Infrastructure\Shared\Doctrine\Repository\AbstractRepository;

/**
 * Class DocumentRepository.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class DocumentRepository extends AbstractRepository implements DocumentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }
}
