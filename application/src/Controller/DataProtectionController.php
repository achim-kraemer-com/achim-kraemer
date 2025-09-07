<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DataProtectionController extends AbstractController
{
    #[Route('/data-protection', name: 'app_data-protection_index')]
    public function index(): Response
    {
        return $this->render('data_protection/index.html.twig', [
            'title'       => 'Datenschutz | Achim Krämer',
            'description' => 'Datenschutzerklärung der Webseite von Achim Krämer. Hier finden Sie alle Informationen zum Datenschutz.',
        ]);
    }
}
