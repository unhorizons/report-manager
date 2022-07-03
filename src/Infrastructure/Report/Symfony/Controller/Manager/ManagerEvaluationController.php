<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Controller\Manager;

use Application\Report\Command\CreateEvaluationCommand;
use Application\Report\Command\DeleteEvaluationCommand;
use Application\Report\Command\UpdateEvaluationCommand;
use Domain\Authentication\Entity\User;
use Domain\Report\Entity\Evaluation;
use Domain\Report\Entity\Report;
use Infrastructure\Report\Symfony\Form\CreateEvaluationForm;
use Infrastructure\Report\Symfony\Form\UpdateEvaluationForm;
use Infrastructure\Shared\Symfony\Controller\AbstractController;
use Infrastructure\Shared\Symfony\Controller\DeleteCsrfTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_REPORT_MANAGER')]
#[Route('/profile/manager/evaluation', name: 'report_manager_evaluation_')]
#[AsController]
final class ManagerEvaluationController extends AbstractController
{
    use DeleteCsrfTrait;

    #[Route('/{report}/new', name: 'new', methods: ['GET', 'POST'])]
    #[Entity('report', options: [
        'mapping' => [
            'report' => 'uuid',
        ],
    ])]
    public function new(Report $report, Request $request): Response
    {
        /** @var User $manager */
        $manager = $this->getUser();
        $command = new CreateEvaluationCommand($manager, $report);
        $form = $this->createForm(CreateEvaluationForm::class, $command)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->dispatchSync($command);
                $this->addFlash('success', $this->translator->trans(
                    id: 'report.flashes.evaluation_created_successfully',
                    parameters: [],
                    domain: 'report'
                ));

                return $this->redirectSeeOther('report_manager_report_show', [
                    'uuid' => $report->getUuid(),
                ]);
            } catch (\Throwable $e) {
                $this->handleUnexpectedException($e);
                $response = new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        return $this->render(
            view: 'domain/report/manager/evaluation/new.html.twig',
            parameters: [
                'report' => $report,
                'form' => $form->createView(),
            ],
            response: $this->getResponseBasedOnFormValidationStatus($form, $response ?? null)
        );
    }

    #[Route('/{report}/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    #[Entity('report', options: [
        'mapping' => [
            'report' => 'uuid',
        ],
    ])]
    public function edit(Report $report, Evaluation $evaluation, Request $request): Response
    {
        $command = new UpdateEvaluationCommand($evaluation);
        $form = $this->createForm(UpdateEvaluationForm::class, $command)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->dispatchSync($command);
                $this->addFlash('success', $this->translator->trans(
                    id: 'report.flashes.evaluation_updated_successfully',
                    parameters: [],
                    domain: 'report'
                ));

                return $this->redirectSeeOther('report_manager_report_show', [
                    'uuid' => $report->getUuid(),
                ]);
            } catch (\Throwable $e) {
                $this->handleUnexpectedException($e);
                $response = new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        return $this->render(
            view: 'domain/report/manager/evaluation/edit.html.twig',
            parameters: [
                'report' => $report,
                'form' => $form->createView(),
            ],
            response: $this->getResponseBasedOnFormValidationStatus($form, $response ?? null)
        );
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Evaluation $evaluation, Request $request): Response
    {
        /** @var Report $report */
        $report = $evaluation->getReport();

        if ($this->isDeleteCsrfTokenValid((string) $evaluation->getId(), $request)) {
            try {
                $this->dispatchSync(new DeleteEvaluationCommand($evaluation));
                $this->addFlash('success', $this->translator->trans(
                    id: 'report.flashes.evaluation_deleted_successfully',
                    parameters: [],
                    domain: 'report'
                ));

                return $this->redirectSeeOther('report_manager_report_show', [
                    'uuid' => $report->getUuid(),
                ]);
            } catch (\Throwable $e) {
                $this->handleUnexpectedException($e);
            }
        } else {
            $this->addSomethingWentWrongFlash();
        }

        return $this->redirectSeeOther('report_manager_report_show', [
            'uuid' => $report->getUuid(),
        ]);
    }
}
