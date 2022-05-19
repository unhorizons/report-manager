<?php

declare(strict_types=1);

namespace Infrastructure\Notification\Doctrine\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Domain\Authentication\Entity\User;
use Domain\Notification\Entity\Notification;
use Domain\Notification\Repository\NotificationRepositoryInterface;
use Infrastructure\Shared\Doctrine\Repository\AbstractRepository;

/**
 * Class NotificationRepository.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class NotificationRepository extends AbstractRepository implements NotificationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function saveOrUpdate(Notification $notification): Notification
    {
        if (null === $notification->getUser()) {
            $this->getEntityManager()->persist($notification);

            return $notification;
        }

        $oldNotification = $this->findOneBy([
            'user' => $notification->getUser(),
            'target' => $notification->getTarget(),
        ]);

        if ($oldNotification) {
            $oldNotification->setCreatedAt($notification->getCreatedAt());
            $oldNotification->setUpdatedAt($notification->getCreatedAt());
            $oldNotification->setMessage($notification->getMessage());

            return $oldNotification;
        }
        $this->getEntityManager()->persist($notification);

        return $notification;
    }

    public function findRecentForUser(User $user, array $channels = ['public']): array
    {
        /** @var Notification[] $result */
        $result = array_map(fn ($n) => (clone $n)->setUser($user), $this->createQueryBuilder('n')
            ->orderBy('n.created_at', 'DESC')
            ->setMaxResults(10)
            ->where('n.user = :user')
            ->orWhere('n.user IS NULL AND n.channel IN (:channels)')
            ->setParameter('user', $user)
            ->setParameter('channels', $channels)
            ->getQuery()
            ->getResult());

        return $result;
    }

    public function clean(): int
    {
        return $this->createQueryBuilder('n')
            ->where('n.created_at < :date')
            ->setParameter('date', new \DateTime('-3 month'))
            ->delete(Notification::class, 'n')
            ->getQuery()
            ->execute();
    }
}
