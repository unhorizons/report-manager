<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Controller;

use Domain\Authentication\Entity\User;
use Infrastructure\Shared\Symfony\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

/**
 * Class HomeController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[IsGranted('IS_AUTHENTICATED')]
final class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return match (true) {
            $user->hasRole('ROLE_USER') => $this->redirectToRoute('report_employee_dashboard_index'),
            $user->hasRole('ROLE_REPORT_MANAGER') => $this->redirectToRoute('report_manager_dashboard_index'),
            $user->hasRole('ROLE_ADMIN') => $this->redirectToRoute('report_admin_dashboard_index'),
            default => $this->redirectToRoute('authentication_logout')
        };
    }
}
