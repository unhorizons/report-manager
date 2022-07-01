<?php

declare(strict_types=1);

namespace Infrastructure\Notification\Symfony\Controller;

use Domain\Authentication\Entity\User;
use Domain\Notification\Entity\PushSubscription;
use Domain\Notification\Repository\PushSubscriptionRepositoryInterface;
use Infrastructure\Shared\Symfony\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PushSubscriptionController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class PushSubscriptionController extends AbstractController
{
    #[Route('/notification/push/subscribe', name: 'notification_push_subscribe', methods: ['POST'])]
    public function subscribe(Request $request, PushSubscriptionRepositoryInterface $repository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $subscription = PushSubscription::fromArray(
            data: (array) json_decode($request->getContent(), associative: true),
            user: $user
        );
        $repository->save($subscription);

        return new JsonResponse(status: Response::HTTP_CREATED);
    }

    #[Route('/notification/push/key', name: 'notification_push_key', methods: ['GET'])]
    public function key(): Response
    {
        return new JsonResponse([
            'key' => $_ENV['VAPID_PUBLIC_KEY'],
        ]);
    }
}
