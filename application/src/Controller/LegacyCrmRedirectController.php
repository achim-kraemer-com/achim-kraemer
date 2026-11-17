<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Das CRM (Kunden, Projekte, Rechnungen) lief früher unter /admin auf dieser Seite.
 * Es ist nach freelancer.achim-kraemer.com umgezogen. Eine schmucklose Login-Maske
 * auf der Hauptdomain hat Google Safe Browsing als Phishing eingestuft, deshalb gibt
 * es hier kein Formular mehr, nur noch dauerhafte Weiterleitungen.
 */
class LegacyCrmRedirectController
{
    private const TARGET = 'https://freelancer.achim-kraemer.com/dashboard';

    #[Route('/login', name: 'legacy_login', methods: ['GET', 'POST'])]
    #[Route('/logout', name: 'legacy_logout', methods: ['GET'])]
    #[Route('/admin{rest}', name: 'legacy_admin', requirements: ['rest' => '(/.*)?'], defaults: ['rest' => ''], methods: ['GET', 'POST'])]
    public function __invoke(): RedirectResponse
    {
        return new RedirectResponse(self::TARGET, Response::HTTP_MOVED_PERMANENTLY);
    }
}
