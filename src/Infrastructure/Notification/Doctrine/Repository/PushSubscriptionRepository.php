<?php

declare(strict_types=1);

namespace Infrastructure\Notification\Doctrine\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Domain\Notification\Entity\PushSubscription;
use Domain\Notification\Repository\PushSubscriptionRepositoryInterface;
use Infrastructure\Shared\Doctrine\Repository\AbstractRepository;

/**
 * Class NotificationRepository.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class PushSubscriptionRepository extends AbstractRepository implements PushSubscriptionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PushSubscription::class);
    }

    public function save(object $entity): void
    {
        if ($entity instanceof PushSubscription) {
            /** @var PushSubscription|null $subscription */
            $subscription = $this->findOneBy([
                'endpoint' => $entity->getEndpoint(),
            ]);

            if ($subscription) {
                $subscription
                    ->setExpirationTime($entity->getExpirationTime())
                    ->setKeys($entity->getKeys())
                    ->setUpdatedAt($entity->getCreatedAt());
            } else {
                $subscription = $entity;
            }

            $this->getEntityManager()->persist($subscription);
            $this->getEntityManager()->flush();
        }
    }

    public function deleteSubscriptionByEndpoint(string $endpoint): int
    {
        return intval($this->createQueryBuilder('ps')
            ->delete(PushSubscription::class, 'ps')
            ->where('ps.endpoint = :endpoint')
            ->setParameter('endpoint', $endpoint)
            ->getQuery()
            ->execute());
    }
}
