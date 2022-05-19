<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Controller;

use Domain\Authentication\Entity\User;
use Domain\Report\Repository\EvaluationRepositoryInterface;
use Domain\Report\Repository\ReportRepositoryInterface;
use Domain\Report\ValueObject\Status;
use Infrastructure\Shared\Symfony\Controller\AbstractController;
use Infrastructure\Shared\Symfony\Controller\ChartTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;

#[IsGranted('ROLE_REPORT_MANAGER')]
#[Route('/profile/manager/dashboard', name: 'report_manager_dashboard_')]
final class ManagerDashboardController extends AbstractController
{
    use ChartTrait;

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
        $report_frequency = $this->frequencyFromReportWithStatus($seen_reports, $unseen_reports);

        // visualization
        $evaluation_chart = $this->createEvaluationChart($builder, $evaluations);
        $report_chart = $this->createReportChart($builder, [$seen_reports, $unseen_reports]);
        $evaluation_frequency_chart = $this->createFrequencyChart($builder, $evaluations);
        $report_frequency_chart = $this->createFrequencyChart($builder, $report_frequency);

        return $this->render(
            view: 'domain/report/manager/dashboard.html.twig',
            parameters: [
                'evaluation_chart' => $evaluation_chart,
                'evaluation_frequency_chart' => $evaluation_frequency_chart,
                'report_frequency_chart' => $report_frequency_chart,
                'report_chart' => $report_chart,
                'stats' => $stats,
            ]
        );
    }
}
