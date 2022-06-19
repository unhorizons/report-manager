<?php

declare(strict_types=1);

namespace Application\Report\Handler;

use Application\Report\Command\CreateReportCommand;
use Domain\Authentication\Entity\User;
use Domain\Report\Entity\Document;
use Domain\Report\Entity\Report;
use Domain\Report\Event\ReportCreatedEvent;
use Domain\Report\Exception\ReportForPeriodAlreadyExistsException;
use Domain\Report\Repository\ReportRepositoryInterface;
use Infrastructure\Shared\Symfony\Mailer\Mailer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

#[AsMessageHandler]
final class CreateReportHandler
{
    public function __construct(
        private readonly string $projectDir,
        private readonly ReportRepositoryInterface $repository,
        private readonly TranslatorInterface $translator,
        private readonly UploaderHelper $uploader,
        private readonly Mailer $mailer,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public function __invoke(CreateReportCommand $command): void
    {
        if ($this->repository->findMatchingHashForEmployee($command->employee, $command->period->getHash())) {
            throw new ReportForPeriodAlreadyExistsException();
        }

        $report = (new Report())
            ->setName($command->name)
            ->setDescription($command->description)
            ->setEmployee($command->employee)
            ->setDocuments($command->documents)
            ->setPeriod($command->period);

        /** @var User $manager */
        foreach ($command->managers as $manager) {
            $report->addManager($manager);
        }

        $this->repository->save($report);
        $this->dispatcher->dispatch(new ReportCreatedEvent($report));
        $this->sendReportToManagers($report);
    }

    public function sendReportToManagers(Report $report): void
    {
        /** @var User $employee */
        $employee = $report->getEmployee();

        $email = $this->mailer
            ->createEmail(
                template: 'domain/report/mail/report_created.mail.twig',
                data: [
                    'report' => $report,
                ]
            )
            ->subject($this->translator->trans(
                id: 'report.mails.subjects.report_created',
                parameters: [
                    '%username%' => $employee->getUsername(),
                ],
                domain: 'report'
            ))
            ->replyTo(new Address((string) $employee->getEmail(), (string) $employee->getUsername()));
        $email = $this->toManagers($email, $report);
        $email = $this->attachDocuments($email, $report);

        $this->mailer->send($email);
    }

    private function attachDocuments(Email $email, Report $report): Email
    {
        /** @var Document[] $documents */
        $documents = $report->getDocuments();

        foreach ($documents as $document) {
            $email->attachFromPath(
                path: sprintf('%s/public%s', $this->projectDir, $this->uploader->asset($document, 'file')),
                name: (string) $report->getName(),
                contentType: (string) $document->getFileType()
            );
        }

        return $email;
    }

    private function toManagers(Email $email, Report $report): Email
    {
        /** @var User[] $managers */
        $managers = $report->getManagers();

        foreach ($managers as $manager) {
            $email->addBcc(new Address((string) $manager->getEmail(), (string) $manager->getUsername()));
        }

        return $email;
    }
}
