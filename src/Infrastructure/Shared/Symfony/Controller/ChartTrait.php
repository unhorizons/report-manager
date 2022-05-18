<?php

declare(strict_types=1);

namespace Infrastructure\Shared\Symfony\Controller;

use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

/**
 * Trait ChartTrait
 * @package Infrastructure\Shared\Symfony\Controller
 * @author bernard-ng <bernard@devscast.tech>
 */
trait ChartTrait
{
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
                        'borderColor' => '#9d72ff',
                        'backgroundColor' => '#9d72ff',
                        'data' => array_values($seen),
                    ],
                    [
                        'label' => 'Rapports non lus',
                        'borderColor' => '#eb6459',
                        'backgroundColor' => '#eb6459',
                        'data' => array_values($unseen),
                    ],
                ],
            ])
            ->setOptions([
                'plugins' => [
                    'tooltip' => [
                        'displayColors' => false,
                    ],
                    'legend' => [
                        'display' => true,
                        'labels' => [
                            'boxWidth' => 12,
                            'padding' => 20,
                            'fontColor' => '#6783b8'
                        ]
                    ]
                ],
                'scales' => [
                    'y' => [
                        'display' => true,
                        'ticks' => [
                            'beginAtZero' => true,
                            'fontSize' => 11,
                            'fontColor' => '#9eaecf',
                            'padding' => 10,
                            'min' => 0,
                            'stepSize' => 1,
                        ],
                    ],
                    'x' => [
                        'display' => true,
                        'ticks' => [
                            'fontSize' => 9,
                            'fontColor' => '#9eaecf',
                            'source' => 'auto',
                            'padding' => 10
                        ],
                    ]
                ],
            ]);
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
                        'tension' => 0.4,
                        'backgroundColor' => '#9d72ff',
                        'borderColor' => '#9d72ff',
                        'pointBorderWidth' => 2,
                        'pointHoverRadius' => 4,
                        'pointHoverBorderWidth' => 4,
                        'pointRadius' => 4,
                        'pointHitRadius' => 4,
                        'pointHoverBorderColor' => '#9d72ff',
                        'pointBorderColor' => 'transparent',
                        'pointBackgroundColor' => 'transparent',
                        'pointHoverBackgroundColor' => '#fff',
                        'data' => array_values($evaluations),
                    ],
                ],
            ])
            ->setOptions([
                'plugins' => [
                    'tooltip' => [
                        'displayColors' => false,
                    ],
                    'legend' => [
                        'display' => true,
                        'labels' => [
                            'boxWidth' => 12,
                            'padding' => 20,
                            'fontColor' => '#6783b8'
                        ]
                    ]
                ],
                'scales' => [
                    'y' => [
                        'display' => true,
                        'ticks' => [
                            'beginAtZero' => true,
                            'fontSize' => 11,
                            'fontColor' => '#9eaecf',
                            'padding' => 10,
                            'min' => 0,
                            'stepSize' => 1,
                        ],
                        'gridLines' => [
                            'tickMarkLength' => 0,
                        ],
                    ],
                    'x' => [
                        'display' => false,
                        'ticks' => [
                            'fontSize' => 9,
                            'fontColor' => '#9eaecf',
                            'source' => 'auto',
                            'padding' => 10
                        ],
                        'gridLines' => [
                            'color' => 'transparent',
                            'tickMarkLength' => 0,
                            'zeroLineColor' => 'transparent'
                        ]
                    ]
                ],
            ]);
    }

    private function createFrequencyChart(ChartBuilderInterface $builder, array $data): Chart
    {
        return $builder
            ->createChart(Chart::TYPE_LINE)
            ->setData([
                'labels' => array_keys($data),
                'datasets' => [
                    [
                        ...$this->getFrequencyDatasetOptions(),
                        'data' => array_values($data),
                    ],
                ],
            ])
            ->setOptions($this->getFrequencyChartOptions());
    }

    private function getFrequencyChartOptions(): array
    {
        return [
            'plugins' => [
                'tooltip' => [
                    'enabled' => false,
                ],
                'legend' => [
                    'display' => false,
                    'labels' => [
                        'boxWidth' => 12,
                        'padding' => 20,
                        'fontColor' => '#6783b8'
                    ]
                ],
            ],
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'display' => false,
                    'ticks' => [
                        'beginAtZero' => true,
                        'fontSize' => 12,
                        'fontColor' => '#9eaecf',
                        'padding' => 0
                    ],
                    'gridLines' => [
                        'tickMarkLength' => 0
                    ],
                ],
                'x' => [
                    'display' => false,
                    'ticks' => [
                        'beginAtZero' => true,
                        'fontSize' => 12,
                        'fontColor' => '#9eaecf',
                        'source' => 'auto',
                        'padding' => 0
                    ],
                    'gridLines' => [
                        'color' => 'transparent',
                        'tickMarkLength' => 0,
                        'offsetGridLines' => true
                    ]
                ]
            ],
        ];
    }

    private function getFrequencyDatasetOptions(): array
    {
        return [
            'tension' => 0.3,
            'borderWidth' => 2,
            'pointBorderColor' => 'transparent',
            'pointBackgroundColor' => 'transparent',
            'pointHoverBackgroundColor' => '#fff',
            'pointHoverBorderColor' => '#7de1f8',
            'borderColor' => '#7de1f8',
            'pointBorderWidth' => 1,
            'pointHoverRadius' => 1,
            'pointHoverBorderWidth' => 1,
            'pointRadius' => 1,
            'pointHitRadius' => 1,
            'backgroundColor' => 'rgb(57, 135, 156)',
        ];
    }

    private function frequencyFromReportWithStatus(array $seen, array $unseen): array
    {
        $data = [];
        foreach ($seen as $month => $sr) {
            $data[$month] = $sr + $unseen[$month];
        }

        return $data;
    }
}
