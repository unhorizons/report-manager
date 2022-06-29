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
            $this->getEntityManager()->flush();

            return $notification;
        }

        /** @var Notification|null $oldNotification */
        $oldNotification = $this->findOneBy([
            'user' => $notification->getUser(),
            'target' => $notification->getTarget(),
        ]);

        if ($oldNotification) {
            $oldNotification->setCreatedAt($notification->getCreatedAt() ?? new \DateTimeImmutable());
            $oldNotification->setUpdatedAt($notification->getCreatedAt());
            $oldNotification->setMessage($notification->getMessage());

            return $oldNotification;
        }
        $this->getEntityManager()->persist($notification);
        $this->getEntityManager()->flush();

        return $notification;
    }

    public function findRecentForUser(User $user, array $channels = ['public']): array
    {
        /** @var Notification[] $result */
        $result = $this->createQueryBuilder('n')
            ->orderBy('n.created_at', 'DESC')
            ->setMaxResults(10)
            ->where('n.user = :user')
            ->orWhere('n.user IS NULL AND n.channel IN (:channels)')
            ->setParameter('user', $user)
            ->setParameter('channels', $channels)
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function countUnreadForUser(User $user): int
    {
        $qb = $this->createQueryBuilder('n')
            ->select('COUNT(n.id) AS count');

        if (null === $user->getNotificationsReadAt()) {
            $qb->where('n.created_at > :last_read')
                ->setParameter('last_read', $user->getNotificationsReadAt());
        } else {
            $qb->where('n.created_at <= :last_read')
                ->setParameter('last_read', new \DateTimeImmutable());
        }

        return intval($qb->getQuery()->getSingleColumnResult());
    }

    public function clean(): int
    {
        return intval($this->createQueryBuilder('n')
            ->where('n.created_at < :date')
            ->setParameter('date', new \DateTimeImmutable('-3 month'))
            ->delete(Notification::class, 'n')
            ->getQuery()
            ->execute());
    }
}
