<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Controller;

use Domain\Authentication\Entity\User;
use Domain\Report\Repository\ReportRepositoryInterface;
use Domain\Report\ValueObject\Status;
use Infrastructure\Shared\Symfony\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[IsGranted('ROLE_USER')]
#[Route('/profile/employee/dashboard', name: 'report_employee_dashboard_', methods: ['GET'])]
final class EmployeeDashboardController extends AbstractController
{
    #[Route('', name: 'index')]
    public function __invoke(ReportRepositoryInterface $repository, ChartBuilderInterface $builder): Response
    {
        /** @var User $employee */
        $employee = $this->getUser();
        $stats = $repository->findCurrentYearStatsForEmployee($employee);
        $seen_reports = $repository->findCurrentYearReportStatsForEmployee($employee, Status::seen());
        $unseen_reports = $repository->findCurrentYearReportStatsForEmployee($employee, Status::unseen());

        $chart = $builder
            ->createChart(Chart::TYPE_BAR)
            ->setData([
                'labels' => array_keys($seen_reports),
                'datasets' => [
                    [
                        'label' => 'Rapports lus',
                        'backgroundColor' => 'rgb(57, 135, 156)',
                        'data' => array_values($seen_reports),
                    ],
                    [
                        'label' => 'Rapports non lus',
                        'backgroundColor' => 'rgb(235, 100, 89)',
                        'data' => array_values($unseen_reports),
                    ],
                ],
            ])
            ->setOptions([
                'scales' => [
                    'y' => [
                        'suggestedMin' => 0,
                        'suggestedMax' => 6,
                    ],
                ],
            ]);

        return $this->render(
            view: 'domain/report/user/dashboard.html.twig',
            parameters: [
                'chart' => $chart,
                'stats' => $stats
            ]
        );
    }
}
