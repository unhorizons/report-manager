<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Controller\Admin;

use Domain\Report\Repository\ReportRepositoryInterface;
use Infrastructure\Shared\Symfony\Controller\AbstractController;
use Infrastructure\Shared\Symfony\Controller\ChartTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;

#[IsGranted('ROLE_ADMIN')]
#[Route('/profile/admin/dashboard', name: 'report_admin_dashboard_', methods: ['GET'])]
#[AsController]
final class AdminDashboardController extends AbstractController
{
    use ChartTrait;

    #[Route('', name: 'index')]
    public function __invoke(ReportRepositoryInterface $repository, ChartBuilderInterface $builder): Response
    {
        $data = $repository->findStats();
        $chart = $this->createReportChart($builder, $repository->findCurrentYearFrequency());

        return $this->render(
            view: 'domain/report/admin/dashboard.html.twig',
            parameters: [
                'data' => $data,
                'chart' => $chart,
            ]
        );
    }
}
