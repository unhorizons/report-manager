<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Controller\Manager;

use Application\Report\Command\SearchReportCommand;
use Domain\Authentication\Entity\User;
use Domain\Report\Entity\Report;
use Domain\Report\ValueObject\Period;
use Infrastructure\Report\Symfony\Form\SearchReportForm;
use Infrastructure\Shared\Symfony\Controller\AbstractController;
use Infrastructure\Shared\Symfony\Controller\PaginationAssertionTrait;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_REPORT_MANAGER')]
#[Route('/profile/manager/search', name: 'report_manager_report_search_')]
#[AsController]
final class ManagerSearchController extends AbstractController
{
    use PaginationAssertionTrait;

    #[Route('', name: 'index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        /** @var User $manager */
        $manager = $this->getUser();
        $command = new SearchReportCommand(
            user: $manager,
            period: Period::createForPreviousWeek()
        );
        $form = $this->createForm(SearchReportForm::class, $command)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                /** @var Envelope $envelop */
                $envelop = $this->dispatchSync($command);

                /** @var HandledStamp $stamp */
                $stamp = $envelop->last(HandledStamp::class);

                /** @var Report[] $result */
                $result = $stamp->getResult();
                $request->getSession()->set('report_search_result', $result);

                return $this->redirectSeeOther('report_manager_report_search_result');
            } catch (\Throwable $e) {
                $this->handleUnexpectedException($e);
                $response = new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        return $this->render(
            view: 'domain/report/manager/search.html.twig',
            parameters: [
                'form' => $form->createView(),
            ],
            response: $this->getResponseBasedOnFormValidationStatus($form, $response ?? null)
        );
    }

    #[Route('/result', name: 'result', methods: ['GET'])]
    public function result(Request $request, PaginatorInterface $paginator): Response
    {
        $result = $request->getSession()->get('report_search_result');
        $page = $request->query->getInt('page', 1);
        $this->assertIsGreaterThanZero($page);

        $data = $paginator->paginate(
            target: $result,
            page: $page,
            limit: 20
        );

        return $this->render(
            view: 'domain/report/manager/search_result.html.twig',
            parameters: [
                'data' => $data,
            ]
        );
    }
}
