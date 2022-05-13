<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Controller;

use Domain\Authentication\Entity\User;
use Domain\Report\Repository\ReportRepositoryInterface;
use Infrastructure\Shared\Symfony\Controller\AbstractController;
use Infrastructure\Shared\Symfony\Controller\PaginationAssertionTrait;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_REPORT_MANAGER')]
#[Route('/profile/manager/search', name: 'report_manager_report_search', methods: ['GET'])]
final class ManagerSearchController extends AbstractController
{
    use PaginationAssertionTrait;

    public function __invoke(Request $request, PaginatorInterface $paginator, ReportRepositoryInterface $repository): Response
    {
        /** @var User $manager */
        $manager = $this->getUser();
        $page = $request->query->getInt('page', 1);
        $query = $request->query->getAlnum('query', '');
        $this->assertIsGreaterThanZero($page);

        $data = $paginator->paginate(
            target: $repository->searchForManager($manager, $query),
            page: $page,
            limit: 20
        );

        return $this->render(
            view: 'domain/report/manager/search.html.twig',
            parameters: [
                'data' => $data,
            ]
        );
    }
}
