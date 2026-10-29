<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{
    #[Route('/services/symfony-development', name: 'app_service_symfony')]
    public function serviceSymfony(): Response
    {
        return $this->detail('service', 'symfony', ['Symfony', 'PHP 8.x', 'Doctrine ORM', 'Twig', 'Docker']);
    }

    #[Route('/services/api-development', name: 'app_service_api')]
    public function serviceApi(): Response
    {
        return $this->detail('service', 'api', ['REST', 'GraphQL', 'Symfony Messenger', 'PDF', 'Docker']);
    }

    #[Route('/services/ai-integration', name: 'app_service_ai')]
    public function serviceAi(): Response
    {
        return $this->detail('service', 'ai', ['Agentic AI', 'RAG', 'OpenAI API', 'PHP 8.x', 'Symfony']);
    }

    #[Route('/references/mirwena', name: 'app_reference_mirwena')]
    public function referenceMirwena(): Response
    {
        return $this->detail('reference', 'mirwena', ['Symfony', 'PHP 8.x', 'Doctrine ORM', 'MySQL / MariaDB', 'Docker'], 'https://mirwena.de');
    }

    #[Route('/references/freelancer-crm', name: 'app_reference_freelancer')]
    public function referenceFreelancer(): Response
    {
        return $this->detail('reference', 'freelancer', ['Symfony 8', 'PHP 8.5', 'Doctrine ORM', 'MariaDB', 'Docker'], 'https://freelancer.achim-kraemer.com');
    }

    #[Route('/references/email-signature', name: 'app_reference_email_signature')]
    public function referenceEmailSignature(): Response
    {
        return $this->detail('reference', 'signature', ['Symfony', 'PHP 8.x', 'Twig', 'JavaScript'], 'https://email-signatur.achim-kraemer.com');
    }

    /**
     * @param list<string> $tech
     */
    private function detail(string $type, string $id, array $tech, ?string $externalUrl = null): Response
    {
        return $this->render('page/detail.html.twig', [
            'type'         => $type,
            'id'           => $id,
            'tech'         => $tech,
            'external_url' => $externalUrl,
        ]);
    }
}
