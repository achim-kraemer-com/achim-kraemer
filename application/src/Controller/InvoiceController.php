<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\TimeEntry;
use App\Entity\User;
use App\Form\InvoiceType;
use App\Repository\InvoiceRepository;
use App\Repository\TimeEntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mime\FileinfoMimeTypeGuesser;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/invoice', name: 'admin_invoice_')]
final class InvoiceController extends AbstractController
{
    #[Route(name: 'index', methods: ['GET'])]
    public function index(InvoiceRepository $invoiceRepository): Response
    {
        return $this->render('invoice/index.html.twig', [
            'invoices' => $invoiceRepository->findAll(),
        ]);
    }

    #[Route('/new/{projectId}', name: 'invoice_new', methods: ['GET', 'POST'])]
    public function new(
        int $projectId,
        Request $request,
        TimeEntryRepository $timeEntryRepository,
        EntityManagerInterface $em,
    ): Response {
        // Finde alle offenen Arbeitsstunden für das angegebene Projekt
        $openTimeEntries = $timeEntryRepository->findBy([
            'project'  => $projectId,
            'invoiced' => false,
        ]);

        $invoice = new Invoice();
        $form    = $this->createForm(InvoiceType::class, $invoice, [
            'time_entries' => $openTimeEntries, // übergebe die offenen Stunden an das Formular
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Verknüpfe die ausgewählten Arbeitsstunden mit der Rechnung
            $selectedTimeEntries = $form->get('time_entries')->getData();

            foreach ($selectedTimeEntries as $timeEntry) {
                $timeEntry->setInvoiced(true); // Markiere als verrechnet
                $invoice->addTimeEntry($timeEntry); // Verknüpfe mit der Rechnung
            }

            // Speichere Rechnung und aktualisiere Stunden in der Datenbank
            $em->persist($invoice);
            $em->flush();

            return $this->redirectToRoute('admin_invoice_index');
        }

        return $this->render('invoice/new.html.twig', [
            'form'            => $form->createView(),
            'openTimeEntries' => $openTimeEntries,
        ]);
    }

    //    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    //    public function new(Request $request, EntityManagerInterface $entityManager): Response
    //    {
    //        $invoice = new Invoice();
    //        $form    = $this->createForm(InvoiceType::class, $invoice);
    //        $form->handleRequest($request);
    //
    //        if ($form->isSubmitted() && $form->isValid()) {
    //            $entityManager->persist($invoice);
    //            $entityManager->flush();
    //
    //            return $this->redirectToRoute('admin_invoice_index', [], Response::HTTP_SEE_OTHER);
    //        }
    //
    //        return $this->render('invoice/new.html.twig', [
    //            'invoice' => $invoice,
    //            'form'    => $form,
    //        ]);
    //    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Invoice $invoice): Response
    {
        return $this->render('invoice/show.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    #[Route('/{id}/payed', name: 'payed', methods: ['GET', 'POST'])]
    public function payed(Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $invoice->setStatus(Invoice::STATUS_PAYED);
        $timeEntries = $invoice->getTimeEntry();
        foreach ($timeEntries as $timeEntry) {
            $timeEntry->setStatus(TimeEntry::STATUS_PAYED);
            $timeEntry->setInvoiced(true);
            $entityManager->persist($timeEntry);
        }
        $entityManager->persist($invoice);
        $entityManager->flush();

        return $this->redirectToRoute('admin_invoice_show', ['id' => $invoice->getId()]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_invoice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('invoice/edit.html.twig', [
            'invoice' => $invoice,
            'form'    => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$invoice->getId(), $request->getPayload()->getString('_token'))) {
            $timeEntries = $invoice->getTimeEntry();
            foreach ($timeEntries as $timeEntry) {
                $timeEntry->setStatus(TimeEntry::STATUS_OPEN);
                $timeEntry->setInvoiced(false);
                $entityManager->persist($timeEntry);
            }
            $entityManager->remove($invoice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_invoice_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/{invoice}/download', name: 'download')]
    public function download(
        Invoice $invoice,
        ContainerBagInterface $params,
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new \LogicException();
        }

        $downloadPath = $params->get('invoices_path');

        $filename = 'Rechnung-'.$invoice->getName().'.pdf';

        $filePath = $downloadPath.$filename;

        $response = new BinaryFileResponse($filePath);

        $mimeTypeGuesser = new FileinfoMimeTypeGuesser();

        if ($mimeTypeGuesser->isGuesserSupported()) {
            $response->headers->set('Content-Type', $mimeTypeGuesser->guessMimeType($filePath));
        } else {
            $response->headers->set('Content-Type', 'application/pdf');
        }

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        return $response;
    }
}
