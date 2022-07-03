<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Controller\Manager;

use Application\Report\Command\SetReportSeenStatusCommand;
use Domain\Authentication\Entity\User;
use Domain\Report\Entity\Report;
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
#[Route('/profile/manager/reports', name: 'report_manager_report_')]
#[AsController]
final class ManagerReportController extends AbstractController
{
    use PaginationAssertionTrait;

    #[Route('/{status?all}', name: 'index', requirements: [
        'status' => 'seen|unseen|all',
    ], methods: ['GET'])]
    public function index(string $status, Request $request, PaginatorInterface $paginator, ReportRepositoryInterface $repository): Response
    {
        /** @var User $manager */
        $manager = $this->getUser();
        $page = $request->query->getInt('page', 1);
        $this->assertIsGreaterThanZero($page);

        $data = $paginator->paginate(
            target: $repository->findAllWithStatusForManager($status, $manager),
            page: $page,
            limit: 20
        );

        $this->assertNonEmptyData($page, $data);

        return $this->render(
            view: 'domain/report/manager/index.html.twig',
            parameters: [
                'data' => $data,
            ]
        );
    }

    #[Route('/{uuid}', name: 'show', methods: ['GET'])]
    public function show(Report $report): Response
    {
        try {
            $this->dispatchSync(new SetReportSeenStatusCommand($report));

            return $this->render(
                view: 'domain/report/manager/show.html.twig',
                parameters: [
                    'data' => $report,
                ]
            );
        } catch (\Throwable $e) {
            $this->handleUnexpectedException($e);

            return $this->redirectSeeOther('report_manager_report_index');
        }
    }
}
