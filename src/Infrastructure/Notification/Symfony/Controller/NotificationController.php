<?php

declare(strict_types=1);

namespace Infrastructure\Notification\Symfony\Controller;

use Application\Notification\Service\NotificationService;
use Domain\Authentication\Entity\User;
use Domain\Notification\Repository\NotificationRepositoryInterface;
use Infrastructure\Shared\Symfony\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/profile/notifications', name: 'notification_')]
final class NotificationController extends AbstractController
{
    #[Route('{status?all}', name: 'index', requirements: [
        'status' => 'all|unseen',
    ], methods: ['GET'])]
    public function index(
        Request $request,
        PaginatorInterface $pagination,
        NotificationRepositoryInterface $repository
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render(
            view: 'domain/notification/index.html.twig',
            parameters: [
                'notifications' => $pagination->paginate(
                    target: $repository->findRecentForUser($user),
                    page: $request->query->getInt('page', 1),
                    limit: 20
                )
            ]
        );
    }

    #[Route('/set_as_read', name: 'read', methods: ['POST'])]
    public function read(NotificationService $notificationService): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $notificationService->readAll($user);
        $this->addFlash('success', $this->translator->trans(
            id: 'notification.flashes.set_as_read_successfully',
            parameters: [],
            domain: 'notification'
        ));

        return $this->redirectSeeOther('notification_index');
    }
}
