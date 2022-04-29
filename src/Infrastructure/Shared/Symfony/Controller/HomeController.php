<?php

declare(strict_types=1);

namespace Infrastructure\Shared\Symfony\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[IsGranted('IS_AUTHENTICATED')]
final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('domain/home.html.twig');
    }
}
