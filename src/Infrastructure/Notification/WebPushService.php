<?php

declare(strict_types=1);

namespace Infrastructure\Notification;

use Domain\Authentication\Entity\User;
use Domain\Notification\Entity\Notification;
use Domain\Notification\Entity\PushSubscription;
use Domain\Notification\Repository\PushSubscriptionRepositoryInterface;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * class WebPushService.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class WebPushService
{
    private readonly WebPush $webPush;
    private ?string $icon;

    public function __construct(
        private readonly PushSubscriptionRepositoryInterface $repository,
        private readonly LoggerInterface $logger,
        RequestStack $stack
    ) {
        $this->webPush = new WebPush([
            'VAPID' => [
                'subject' => 'mailto:rapport@unhorizons.org',
                'publicKey' => $_ENV['VAPID_PUBLIC_KEY'],
                'privateKey' => $_ENV['VAPID_PRIVATE_KEY']
            ]
        ]);
        $this->icon = $stack->getCurrentRequest()?->getUriForPath('/images/logo_icon.png');
    }

    public function notifyChannel(Notification $notification): void
    {
        /** @var PushSubscription[] $subscription */
        $subscriptions = $this->repository->findAll();

        try {
            foreach ($subscriptions as $subscription) {
                $this->push($subscription, $notification);
            }

            foreach ($this->webPush->flush() as $report) {
                if (!$report->isSuccess()) {
                    $this->repository->delete($subscription);
                    dd($report);
                } else {
                    dd($report);
                }
            }
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
            dd($e);
        }
    }

    public function notifyUser(Notification $notification, User $user): void
    {
        /** @var PushSubscription[] $subscription */
        $subscriptions = $this->repository->findBy(['user' => $user]);

        try {
            foreach ($subscriptions as $subscription) {
                $this->push($subscription, $notification);
            }

            foreach ($this->webPush->flush() as $report) {
                if (!$report->isSuccess()) {
                    $this->repository->delete($subscription);
                }
            }
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
    }

    /**
     * @throws \ErrorException
     */
    private function push(PushSubscription $subscription, Notification $notification): void
    {
        $this->webPush->queueNotification(
            subscription: Subscription::create([
                'endpoint' => $subscription->getEndpoint(),
                'publicKey' => $subscription->getKeys()->p256dh,
                'authToken' => $subscription->getKeys()->auth
            ]),
            payload: json_encode([
                'title' => 'UNH Rapport',
                'options' => [
                    'body' => $notification->getMessage(),
                    'data' => [
                        "url" => $notification->getUrl(),
                    ],
                    'actions' => [
                        ["action" => "show", "title" => "Voir les détails"]
                    ],
                    "icon" => $this->icon,
                    'requireInteraction' => true,
                    'timestamp' => $notification->getCreatedAt()->format('u'),
                    'lang' => 'FR'
                ],
            ])
        );
    }
}
