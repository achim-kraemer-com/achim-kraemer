<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * llms.txt: kompakte Zusammenfassung der Website für KI-Suchsysteme (GEO).
 * Aus Twig gerendert, damit Preise und Texte mit den Übersetzungen übereinstimmen.
 */
class LlmsController extends AbstractController
{
    #[Route('/llms.txt', name: 'llms_txt', defaults: ['_locale' => 'de'])]
    public function index(): Response
    {
        return $this->render('llms/llms.txt.twig', [], new Response('', Response::HTTP_OK, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]));
    }
}
