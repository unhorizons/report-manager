<?php

declare(strict_types=1);

namespace Infrastructure\Administration\Symfony\Controller;

use Infrastructure\Shared\Symfony\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/profile/admin/dashboard")]
final class DashboardController extends AbstractController
{
    #[Route("", name: "administration_dashboard_index", methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('domain/administration/dashboard.html.twig');
    }
}
