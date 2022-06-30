<?php

declare(strict_types=1);

namespace Infrastructure\Shared\Symfony\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class OfflineController extends AbstractController
{
    #[Route('/offline')]
    public function offline(): Response
    {
        return $this->render('shared/offline.html.twig');
    }
}
