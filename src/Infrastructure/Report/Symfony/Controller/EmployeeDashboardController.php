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

#[IsGranted('ROLE_USER')]
#[Route('/profile/employee/dashboard', name: 'report_employee_dashboard_', methods: ['GET'])]
final class EmployeeDashboardController extends AbstractController
{
    use ChartTrait;

    #[Route('', name: 'index')]
    public function __invoke(
        ReportRepositoryInterface $reportRepository,
        EvaluationRepositoryInterface $evaluationRepository,
        ChartBuilderInterface $builder
    ): Response {
        /** @var User $employee */
        $employee = $this->getUser();
        $stats = $reportRepository->findCurrentYearStatsForEmployee($employee);
        $seen_reports = $reportRepository->findCurrentYearStatsForEmployeeWithStatus($employee, Status::seen());
        $unseen_reports = $reportRepository->findCurrentYearStatsForEmployeeWithStatus($employee, Status::unseen());
        $report_frequency = $this->frequencyFromReportWithStatus($seen_reports, $unseen_reports);
        $evaluation_frequency = $evaluationRepository->findCurrentYearFrequencyForEmployee($employee);

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
