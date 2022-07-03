<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Controller\Admin;

use Domain\Authentication\Entity\User;
use Domain\Report\Repository\EvaluationRepositoryInterface;
use Domain\Report\Repository\ReportRepositoryInterface;
use Domain\Report\ValueObject\Status;
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
    public function __invoke(
        ReportRepositoryInterface $reportRepository,
        EvaluationRepositoryInterface $evaluationRepository,
        ChartBuilderInterface $builder
    ): Response {
        /** @var User $admin */
        $admin = $this->getUser();
        $stats = $reportRepository->findCurrentYearStatsForEmployee($admin);
        $seen_reports = $reportRepository->findCurrentYearStatsForEmployeeWithStatus($admin, Status::seen());
        $unseen_reports = $reportRepository->findCurrentYearStatsForEmployeeWithStatus($admin, Status::unseen());
        $report_frequency = $this->frequencyFromReportWithStatus($seen_reports, $unseen_reports);
        $evaluation_frequency = $evaluationRepository->findCurrentYearFrequencyForEmployee($admin);

        // visualization
        $chart = $this->createReportChart($builder, [$seen_reports, $unseen_reports]);
        $report_frequency_chart = $this->createFrequencyChart($builder, $report_frequency);
        $evaluation_frequency_chart = $this->createFrequencyChart($builder, $evaluation_frequency);

        return $this->render(
            view: 'domain/report/user/dashboard.html.twig',
            parameters: [
                'chart' => $chart,
                'report_frequency_chart' => $report_frequency_chart,
                'evaluation_frequency_chart' => $evaluation_frequency_chart,
                'stats' => $stats,
            ]
        );
    }
}
