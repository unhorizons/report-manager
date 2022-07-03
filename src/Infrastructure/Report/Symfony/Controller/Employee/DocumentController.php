<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Controller\Employee;

use Application\Report\Command\DeleteDocumentCommand;
use Domain\Report\Entity\Document;
use Infrastructure\Shared\Symfony\Controller\AbstractController;
use Infrastructure\Shared\Symfony\Controller\DeleteCsrfTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
#[Route('/profile/employee/documents', name: 'report_document_')]
#[AsController]
final class DocumentController extends AbstractController
{
    use DeleteCsrfTrait;

    #[Route('/{uuid}', name: 'delete', methods: ['DELETE'])]
    public function delete(Document $document, Request $request): Response
    {
        $this->denyAccessUnlessGranted('DOCUMENT_DELETE', $document);
        $report = $document->getReport()?->getUuid();

        if ($this->isDeleteCsrfTokenValid((string) $document->getId(), $request)) {
            try {
                $this->dispatchSync(new DeleteDocumentCommand($document));
                $this->addFlash('success', $this->translator->trans(
                    id: 'report.flashes.document_deleted_successfully',
                    parameters: [],
                    domain: 'report'
                ));
            } catch (\Throwable $e) {
                $this->handleUnexpectedException($e);
            }
        } else {
            $this->addSomethingWentWrongFlash();
        }

        return $this->redirectSeeOther('report_employee_report_show', [
            'uuid' => $report,
        ]);
    }
}
