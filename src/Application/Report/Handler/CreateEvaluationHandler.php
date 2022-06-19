<?php

declare(strict_types=1);

namespace Application\Report\Handler;

use Application\Report\Command\CreateEvaluationCommand;
use Domain\Report\Entity\Evaluation;
use Domain\Report\Entity\Report;
use Domain\Report\Event\EvaluationCreatedEvent;
use Domain\Report\Repository\EvaluationRepositoryInterface;
use Infrastructure\Shared\Symfony\Mailer\Mailer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsMessageHandler]
final class CreateEvaluationHandler
{
    public function __construct(
        private readonly Mailer $mailer,
        private readonly TranslatorInterface $translator,
        private readonly EvaluationRepositoryInterface $repository,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public function __invoke(CreateEvaluationCommand $command): void
    {
        $evaluation = Evaluation::createForReport(
            content: (string) $command->content,
            manager: $command->manager,
            report: $command->report
        );

        $this->repository->save($evaluation);
        $this->dispatcher->dispatch(new EvaluationCreatedEvent($evaluation));
        $this->sendEvaluationNotificationToEmployee($command->report, $evaluation);
    }

    private function sendEvaluationNotificationToEmployee(Report $report, Evaluation $evaluation): void
    {
        $employee = $report->getEmployee();
        $email = $this->mailer->createEmail(
            template: 'domain/report/mail/evaluation_created.mail.twig',
            data: [
                'report' => $report,
                'evaluation' => $evaluation,
            ]
        )->subject($this->translator->trans(
            id: 'report.mails.subjects.evaluation_created',
            parameters: [
                '%username%' => $employee?->getUsername(),
            ],
            domain: 'report'
        ))->to(new Address((string) $employee?->getEmail(), (string) $employee?->getUsername()));

        $this->mailer->send($email);
    }
}
