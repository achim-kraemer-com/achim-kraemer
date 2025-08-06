<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, MailerInterface $mailer, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Verarbeitung des Formulars, z.B. Senden einer E-Mail
            $data  = $form->getData();
            $email = (new TemplatedEmail())
                ->from($data['email'])
                ->to('kontakt@achim-kraemer.com')
                ->subject('Contact Form Message')
                ->htmlTemplate('emails/contact.html.twig')
                ->context(
                    [
                        'name'    => $data['name'],
                        'phone'   => $data['phone'],
                        'mail'    => $data['email'],
                        'message' => $data['message'],
                    ]
                );

            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                $email = (new Email())
                    ->from('kontakt@achim-kraemer.com')
                    ->to('kontakt@achim-kraemer.com')
                    ->subject('Es gab anscheinend einen Fehler beim Versenden einer KontaktMail')
                    ->text('Es gab anscheinend einen Fehler beim Versenden einer KontaktMail: '.$e->getMessage());

                $mailer->send($email);
            }

            $this->addFlash('success', $translator->trans('app.contact.email_send'));

            return $this->redirect($this->generateUrl('app_home').'#contact');
        }

        return $this->render('home/index.html.twig', [
            'form' => $form,
            'title' => 'Symfony Entwickler & Freelancer | Achim Krämer',
            'description' => 'Symfony Freelancer gesucht? Ich unterstütze ihre Projekt und bringe über 15 Jahre PHP-Erfahrung mit ein.'
        ]);
    }

    #[Route('/api/keywords', name: 'api_keywords')]
    public function keywords(RequestStack $requestStack): JsonResponse
    {
        // Begriffe definieren
        $keywordList = [
            'JavaScript', 'PHP', 'HTML', 'CSS', 'Symfony', 'Twig', 'Doctrine', 'Symfony 7+', 'Doctrine ORM',
            'API Platform', 'Event Listener & Subscriber', 'Symfony Messenger', 'Symfony Security', 'Symfony Forms',
            'Symfony Console Commands', 'Symfony Flex', 'jQuery', 'AJAX & Fetch API', 'jQuery UI', 'DataTables',
            'jQuery Plugins', 'Webpack Encore', 'Bootstrap', 'Google Cloud Platform (GCP)', 'Cloud Storage',
            'Cloud Firestore', 'Cloud Functions', 'Docker & Docker Compose', 'GIT', 'GitHub/GitLab', 'PHP 8.x+',
            'Composer & Autoloading', 'REST & GraphQL APIs', 'WebSockets', 'Microservices Architektur',
            'SOLID', 'Clean Code & Design Patterns', 'AI', 'KI',
        ];

        // Zufällige Begriffe ausgeben
        $keywords = $this->getRandomKeywords($keywordList, 9);

        return $this->json($keywords);
    }

    private function getRandomKeywords(array $keywordList, int $count = 7): array
    {
        \shuffle($keywordList); // Durchmischen des Arrays

        return \array_slice($keywordList, 0, $count); // Die ersten 7 Elemente zurückgeben
    }
}
