<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class SitemapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'sitemap', defaults: ['_format' => 'xml'])]
    public function index(RouterInterface $router): Response
    {
        $routes = $router->getRouteCollection();
        $urls   = [];

        foreach ($routes as $routeName => $route) {
            // Symfony-interne Routen ignorieren (z. B. _profiler, _wdt)
            if (\str_starts_with($routeName, '_')
                || \str_starts_with($routeName, 'admin')
                || \str_starts_with($routeName, 'app_login')
                || \str_starts_with($routeName, 'app_logout')
                || \str_starts_with($routeName, 'api')
                || \str_starts_with($routeName, 'sitemap')
                || \str_starts_with($routeName, 'upload')
                || \str_starts_with($routeName, 'swagger')
                || \str_starts_with($routeName, 'fos_user')) {
                continue;
            }

            // Versuche, eine absolute URL für die Route zu generieren
            try {
                $urls[] = [
                    'loc'      => $this->generateUrl($routeName, [], RouterInterface::ABSOLUTE_URL),
                    'priority' => '0.8',
                ];
            } catch (\Exception $e) {
                // Falls die Route Parameter benötigt, überspringen wir sie
                continue;
            }
        }

        return $this->render('sitemap/sitemap.xml.twig', [
            'urls' => $urls,
        ], new Response('', Response::HTTP_OK, ['Content-Type' => 'application/xml']));
    }
}
