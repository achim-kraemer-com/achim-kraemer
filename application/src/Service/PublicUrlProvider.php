<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Liefert alle öffentlichen, indexierbaren Seiten - Grundlage für sitemap.xml und IndexNow.
 */
class PublicUrlProvider
{
    private const PRIORITIES = [
        'app_home' => '1.0',
        'app_cv'   => '0.5',
    ];

    // Rechtstexte sind noindex und gehören deshalb weder in die Sitemap noch zu IndexNow.
    private const NOINDEX = ['app_impress_index', 'app_data-protection_index'];

    public function __construct(private readonly RouterInterface $router)
    {
    }

    /**
     * @return array<string, array{priority: string, alternates: array<string, string>}>
     */
    public function getPages(): array
    {
        $urls = [];

        foreach ($this->router->getRouteCollection() as $routeName => $route) {
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
                $url = $this->router->generate($routeName, [], UrlGeneratorInterface::ABSOLUTE_URL);
            } catch (\Exception) {
                // Falls die Route Parameter benötigt, überspringen wir sie
                continue;
            }

            $urls[$baseRoute]['priority']            = self::PRIORITIES[$baseRoute] ?? '0.8';
            $urls[$baseRoute]['alternates'][$locale] = $url;
        }

        return $urls;
    }

    /**
     * @return list<string>
     */
    public function getUrls(): array
    {
        $urls = [];
        foreach ($this->getPages() as $page) {
            \array_push($urls, ...\array_values($page['alternates']));
        }

        return \array_values(\array_unique($urls));
    }
}
