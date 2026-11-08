<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\PublicUrlProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'sitemap', defaults: ['_format' => 'xml'])]
    public function index(PublicUrlProvider $urlProvider): Response
    {
        return $this->render('sitemap/sitemap.xml.twig', [
            'urls' => $urlProvider->getPages(),
        ], new Response('', Response::HTTP_OK, ['Content-Type' => 'application/xml']));
    }
}
