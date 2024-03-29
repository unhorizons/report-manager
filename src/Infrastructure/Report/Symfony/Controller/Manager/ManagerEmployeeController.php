<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Controller\Manager;

use Domain\Authentication\Entity\User;
use Domain\Authentication\Repository\UserRepositoryInterface;
use Domain\Report\Repository\ReportRepositoryInterface;
use Infrastructure\Shared\Symfony\Controller\AbstractController;
use Infrastructure\Shared\Symfony\Controller\PaginationAssertionTrait;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_REPORT_MANAGER')]
#[Route('/profile/manager/employee', name: 'report_manager_employee_')]
#[AsController]
final class ManagerEmployeeController extends AbstractController
{
    use PaginationAssertionTrait;

    #[Route('', name: 'index', methods: ['GET'], priority: 10)]
    public function index(Request $request, PaginatorInterface $paginator, UserRepositoryInterface $repository): Response
    {
        /** @var User $manager */
        $manager = $this->getUser();
        $page = $request->query->getInt('page', 1);
        $this->assertIsGreaterThanZero($page);

        $data = $paginator->paginate(
            target: $repository->findAllEmployeeWithStatsForManager($manager),
            page: $page,
            limit: 20
        );

        $this->assertNonEmptyData($page, $data);

        return $this->render(
            view: 'domain/report/manager/employee/index.html.twig',
            parameters: [
                'data' => $data,
            ]
        );
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], priority: 11)]
    public function show(
        User $employee,
        Request $request,
        PaginatorInterface $paginator,
        ReportRepositoryInterface $repository,
    ): Response {
        /** @var User $manager */
        $manager = $this->getUser();
        $page = $request->query->getInt('page', 1);
        $this->assertIsGreaterThanZero($page);

        $data = $paginator->paginate(
            target: $repository->findAllForEmployeeAndManager($manager, $employee),
            page: $page,
            limit: 20
        );

        $this->assertNonEmptyData($page, $data);

        return $this->render(
            view: 'domain/report/manager/employee/show.html.twig',
            parameters: [
                'employee' => $employee,
                'data' => $data,
            ]
        );
    }
}
