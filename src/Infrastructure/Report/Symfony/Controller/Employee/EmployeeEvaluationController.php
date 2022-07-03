<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Controller\Employee;

use Domain\Authentication\Entity\User;
use Domain\Report\Repository\EvaluationRepositoryInterface;
use Infrastructure\Shared\Symfony\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
#[Route('/profile/employee/evaluations', name: 'report_employee_evaluation_')]
#[AsController]
final class EmployeeEvaluationController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        Request $request,
        PaginatorInterface $paginator,
        EvaluationRepositoryInterface $repository
    ): Response {
        /** @var User $employee */
        $employee = $this->getUser();
        $page = $request->query->getInt('page', 1);
        $data = $paginator->paginate(
            target: $repository->findAllEvaluationForEmployee($employee),
            page: $page,
            limit: 20
        );

        return $this->render(
            view: 'domain/report/employee/evaluation.html.twig',
            parameters: [
                'data' => $data,
            ]
        );
    }
}
