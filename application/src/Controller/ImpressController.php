<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImpressController extends AbstractController
{
    #[Route('/impress', name: 'app_impress_index')]
    public function index(): Response
    {
        return $this->render('impress/index.html.twig', [
            'title' => 'Impressum | Achim Krämer',
            'description' => 'Impressum der Webseite von Achim Krämer. Hier finden Sie alle rechtlichen Informationen.'
        ]);
    }
}
