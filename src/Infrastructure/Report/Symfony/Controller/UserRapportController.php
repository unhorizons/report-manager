<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Controller;

use Domain\Authentication\Entity\User;
use Domain\Report\Repository\ReportRepositoryInterface;
use Infrastructure\Shared\Symfony\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[IsGranted('ROLE_USER')]
#[Route('/profile/reports', name: 'report_user_')]
final class UserRapportController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        Request $request,
        PaginatorInterface $paginator,
        ReportRepositoryInterface $repository
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $page = $request->query->getInt('page', 1);
        $data = $paginator->paginate(
            target: $repository->findAllForUser($user),
            page: $page,
            limit: 20
        );

        return $this->render(
            view: 'domain/report/user_index.html.twig',
            parameters: ['data' => $data]
        );
    }
}
