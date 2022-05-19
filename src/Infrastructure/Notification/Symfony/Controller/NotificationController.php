<?php

declare(strict_types=1);

namespace Infrastructure\Notification\Symfony\Controller;

use Domain\Authentication\Entity\User;
use Domain\Notification\Repository\NotificationRepositoryInterface;
use Infrastructure\Shared\Symfony\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/profile/notifications', name: 'notification_')]
final class NotificationController extends AbstractController
{
    #[Route('{status?all}', name: 'index', requirements: [
        'status' => 'all|unseen',
    ], methods: ['GET'])]
    public function index(PaginatorInterface $pagination, NotificationRepositoryInterface $repository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render(
            view: 'domain/notification/index.html.twig',
            parameters: []
        );
    }
}
