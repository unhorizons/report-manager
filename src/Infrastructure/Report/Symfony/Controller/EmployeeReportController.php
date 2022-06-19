<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Controller;

use Application\Report\Command\CreateReportCommand;
use Application\Report\Command\DeleteReportCommand;
use Application\Report\Command\UpdateReportCommand;
use Doctrine\Common\Collections\ArrayCollection;
use Domain\Authentication\Entity\User;
use Domain\Report\Entity\Report;
use Domain\Report\Repository\ReportRepositoryInterface;
use Domain\Report\ValueObject\Period;
use Infrastructure\Report\Symfony\Form\CreateReportForm;
use Infrastructure\Report\Symfony\Form\UpdateReportForm;
use Infrastructure\Shared\Symfony\Controller\AbstractController;
use Infrastructure\Shared\Symfony\Controller\DeleteCsrfTrait;
use Infrastructure\Shared\Symfony\Controller\PaginationAssertionTrait;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
#[Route('/profile/employee/reports', name: 'report_employee_report_')]
final class EmployeeReportController extends AbstractController
{
    use DeleteCsrfTrait;
    use PaginationAssertionTrait;

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator, ReportRepositoryInterface $repository): Response
    {
        /** @var User $employee */
        $employee = $this->getUser();
        $page = $request->query->getInt('page', 1);
        $this->assertIsGreaterThanZero($page);

        $data = $paginator->paginate(
            target: $repository->findAllForEmployee($employee),
            page: $page,
            limit: 15
        );

        $this->assertNonEmptyData($page, $data);

        return $this->render(
            view: 'domain/report/user/index.html.twig',
            parameters: [
                'data' => $data,
            ]
        );
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        /** @var User $employee */
        $employee = $this->getUser();
        $command = new CreateReportCommand(
            employee: $employee,
            period: Period::createForPreviousWeek(),
            managers: new ArrayCollection()
        );
        $form = $this->createForm(CreateReportForm::class, $command)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->dispatchSync($command);
                $this->addFlash('success', $this->translator->trans(
                    id: 'report.flashes.report_created_successfully',
                    parameters: [],
                    domain: 'report'
                ));

                return $this->redirectSeeOther('report_employee_report_index');
            } catch (\Throwable $e) {
                $this->handleUnexpectedException($e);
                $response = new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        return $this->render(
            view: 'domain/report/user/new.html.twig',
            parameters: [
                'form' => $form->createView(),
            ],
            response: $this->getResponseBasedOnFormValidationStatus($form, $response ?? null)
        );
    }

    #[Route('/{uuid}', name: 'show', methods: ['GET'])]
    public function show(Report $report): Response
    {
        $this->denyAccessUnlessGranted('REPORT_VIEW', $report);

        return $this->render(
            view: 'domain/report/user/show.html.twig',
            parameters: [
                'data' => $report,
            ]
        );
    }

    #[Route('/{uuid}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request, Report $report): Response
    {
        $this->denyAccessUnlessGranted('REPORT_DELETE', $report);

        if ($this->isDeleteCsrfTokenValid((string) $report->getId(), $request)) {
            try {
                $this->dispatchSync(new DeleteReportCommand($report));
                $this->addFlash('success', $this->translator->trans(
                    id: 'report.flashes.report_deleted_successfully',
                    parameters: [
                        '%id%' => $report->getId(),
                    ],
                    domain: 'report'
                ));

                return $this->redirectSeeOther('report_employee_report_index');
            } catch (\Throwable $e) {
                $this->handleUnexpectedException($e);
            }
        } else {
            $this->addSomethingWentWrongFlash();
        }

        return $this->redirectSeeOther('report_employee_report_show', [
            'uuid' => $report->getUuid(),
        ]);
    }

    #[Route('/{uuid}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Report $report): Response
    {
        $this->denyAccessUnlessGranted('REPORT_UPDATE', $report);
        $command = new UpdateReportCommand(
            report: $report,
            period: $report->getPeriod(),
            managers: $report->getManagers()
        );
        $form = $this->createForm(UpdateReportForm::class, $command)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->dispatchSync($command);
                $this->addFlash('success', $this->translator->trans(
                    id: 'report.flashes.report_updated_successfully',
                    parameters: [
                        '%id%' => $report->getId(),
                    ],
                    domain: 'report'
                ));

                return $this->redirectSeeOther('report_employee_report_show', [
                    'uuid' => $report->getUuid(),
                ]);
            } catch (\Throwable $e) {
                $this->handleUnexpectedException($e);
                $response = new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        return $this->render(
            view: 'domain/report/user/edit.html.twig',
            parameters: [
                'form' => $form->createView(),
            ],
            response: $this->getResponseBasedOnFormValidationStatus($form, $response ?? null)
        );
    }
}
