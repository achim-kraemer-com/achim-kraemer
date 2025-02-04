<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LocaleController extends AbstractController
{
    #[Route('/switch-locale/{locale}', name: 'switch_locale')]
    public function switchLocale(string $locale): Response
    {
        if (!\in_array($locale, ['de', 'en'], true)) {
            throw $this->createNotFoundException('Locale not supported');
        }

        $localeUrl = 'app_home.de';
        if ($locale === 'en') {
            $localeUrl = 'app_home.en';
        }

        return $this->redirect($this->generateUrl($localeUrl));
    }
}
