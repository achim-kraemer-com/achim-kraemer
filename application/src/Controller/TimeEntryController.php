<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\TimeEntry;
use App\Form\TimeEntryType;
use App\Repository\TimeEntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/time/entry', name: 'admin_time_entry_')]
final class TimeEntryController extends AbstractController
{
    #[Route(name: 'index', methods: ['GET'])]
    public function index(TimeEntryRepository $timeEntryRepository): Response
    {
        return $this->render('time_entry/index.html.twig', [
            'time_entries' => $timeEntryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $timeEntry = new TimeEntry();
        $form      = $this->createForm(TimeEntryType::class, $timeEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($timeEntry);
            $entityManager->flush();

            return $this->redirectToRoute('admin_time_entry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('time_entry/new.html.twig', [
            'time_entry' => $timeEntry,
            'form'       => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(TimeEntry $timeEntry): Response
    {
        return $this->render('time_entry/show.html.twig', [
            'time_entry' => $timeEntry,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TimeEntry $timeEntry, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TimeEntryType::class, $timeEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_time_entry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('time_entry/edit.html.twig', [
            'time_entry' => $timeEntry,
            'form'       => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, TimeEntry $timeEntry, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$timeEntry->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($timeEntry);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_time_entry_index', [], Response::HTTP_SEE_OTHER);
    }
}
