<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Controller;

use Domain\Authentication\Entity\User;
use Domain\Report\Repository\EvaluationRepositoryInterface;
use Domain\Report\Repository\ReportRepositoryInterface;
use Domain\Report\ValueObject\Status;
use Infrastructure\Shared\Symfony\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[IsGranted('ROLE_REPORT_MANAGER')]
#[Route('/profile/manager/dashboard', name: 'report_manager_dashboard_')]
final class ManagerDashboardController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        ChartBuilderInterface $builder,
        ReportRepositoryInterface $reportRepository,
        EvaluationRepositoryInterface $evaluationRepository
    ): Response {
        /** @var User $manager */
        $manager = $this->getUser();
        $stats = $reportRepository->findCurrentYearStatsForManager($manager);
        $evaluations = $evaluationRepository->findCurrentYearEvaluationStatsForManager($manager);
        $seen_reports = $reportRepository->findCurrentYearStatsForManagerWithStatus($manager, Status::seen());
        $unseen_reports = $reportRepository->findCurrentYearStatsForManagerWithStatus($manager, Status::unseen());
        $evaluationChart = $this->createEvaluationChart($builder, $evaluations);
        $reportChart = $this->createReportChart($builder, [$seen_reports, $unseen_reports]);

        return $this->render(
            view: 'domain/report/manager/dashboard.html.twig',
            parameters: [
                'evaluationChart' => $evaluationChart,
                'reportChart' => $reportChart,
                'stats' => $stats
            ]
        );
    }

    private function createEvaluationChart(ChartBuilderInterface $builder, array $evaluations): Chart
    {
        return $builder
            ->createChart(Chart::TYPE_LINE)
            ->setData([
                'labels' => array_keys($evaluations),
                'datasets' => [
                    [
                        'label' => 'Évaluations données',
                        'backgroundColor' => 'rgb(57, 135, 156)',
                        'data' => array_values($evaluations),
                    ],
                ],
            ])
            ->setOptions([
                'scales' => [
                    'y' => [
                        'suggestedMin' => 0,
                        'suggestedMax' => 10,
                    ],
                ],
            ]);
    }

    private function createReportChart(ChartBuilderInterface $builder, array $reports): Chart
    {
        [$seen, $unseen] = $reports;
        return $builder
            ->createChart(Chart::TYPE_BAR)
            ->setData([
                'labels' => array_keys($seen),
                'datasets' => [
                    [
                        'label' => 'Rapports lus',
                        'backgroundColor' => 'rgb(57, 135, 156)',
                        'data' => array_values($seen),
                    ],
                    [
                        'label' => 'Rapports non lus',
                        'backgroundColor' => 'rgb(235, 100, 89)',
                        'data' => array_values($unseen),
                    ],
                ],
            ])
            ->setOptions([
                'scales' => [
                    'y' => [
                        'suggestedMin' => 0,
                        'suggestedMax' => 10,
                    ],
                ],
            ]);
    }
}
