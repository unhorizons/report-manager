<?php

declare(strict_types=1);

namespace Infrastructure\Notification\Symfony\Controller;

use Application\Notification\Command\SetAllNotificationsReadCommand;
use Application\Notification\Command\SetNotificationReadCommand;
use Application\Notification\Service\NotificationService;
use Domain\Authentication\Entity\User;
use Domain\Notification\Entity\Notification;
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
                'data' => $pagination->paginate(
                    target: $repository->findRecentForUser($user),
                    page: $request->query->getInt('page', 1),
                    limit: 20
                )
            ]
        );
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Notification $notification): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($notification->getUser() !== $user) {
            $this->createAccessDeniedException();
        }

        try {
            $this->dispatchSync(new SetNotificationReadCommand($notification));
        } catch (\Throwable $e) {
            $this->handleUnexpectedException($e);
        }

        return $this->redirect($notification->getUrl(), status: Response::HTTP_PERMANENTLY_REDIRECT);
    }

    #[Route('/set_as_read', name: 'read', methods: ['POST'])]
    public function read(NotificationService $notificationService): Response
    {
        try {
            /** @var User $user */
            $user = $this->getUser();
            $this->dispatchSync(new SetAllNotificationsReadCommand($user));
            $this->addFlash('success', $this->translator->trans(
                id: 'notification.flashes.set_as_read_successfully',
                parameters: [],
                domain: 'notification'
            ));
        } catch (\Throwable $e) {
            $this->handleUnexpectedException($e);
        }

        return $this->redirectSeeOther('notification_index');
    }
}
