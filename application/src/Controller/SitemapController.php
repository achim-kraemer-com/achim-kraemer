<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class SitemapController extends AbstractController
{
    private const PRIORITIES = [
        'app_home'                     => '1.0',
        'app_cv'                       => '0.5',
    ];

    // Rechtstexte sind noindex und gehören deshalb nicht in die Sitemap.
    private const NOINDEX = ['app_impress_index', 'app_data-protection_index'];

    #[Route('/sitemap.xml', name: 'sitemap', defaults: ['_format' => 'xml'])]
    public function index(RouterInterface $router): Response
    {
        $urls = [];

        foreach ($router->getRouteCollection() as $routeName => $route) {
            // Nur lokalisierte öffentliche Seiten (app_*.de / app_*.en), keine Visitenkarten-Entwürfe, Logins usw.
            if (!\preg_match('/^(app_.+)\.(de|en)$/', $routeName, $matches)
                || \str_starts_with($matches[1], 'app_businesscard')
                || \str_starts_with($matches[1], 'app_login')
                || \str_starts_with($matches[1], 'app_logout')
                || \in_array($matches[1], self::NOINDEX, true)) {
                continue;
            }

            [, $baseRoute, $locale] = $matches;

            try {
                $urls[$baseRoute]['priority']        = self::PRIORITIES[$baseRoute] ?? '0.8';
                $urls[$baseRoute]['alternates'][$locale] = $this->generateUrl($routeName, [], RouterInterface::ABSOLUTE_URL);
            } catch (\Exception $e) {
                // Falls die Route Parameter benötigt, überspringen wir sie
                unset($urls[$baseRoute]['alternates'][$locale]);
            }
        }

        return $this->render('sitemap/sitemap.xml.twig', [
            'urls' => \array_filter($urls, static fn (array $url): bool => !empty($url['alternates'])),
        ], new Response('', Response::HTTP_OK, ['Content-Type' => 'application/xml']));
    }
}
