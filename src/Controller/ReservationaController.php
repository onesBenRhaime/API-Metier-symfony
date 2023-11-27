<?php

namespace App\Controller;

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reservationa')]
class ReservationaController extends AbstractController
{
    #[Route('/', name: 'app_reservationa_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservationa/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }
    #[Route('/{idRes}', name: 'app_reservationa_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservationa/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }
    #[Route('/{idRes}/edit', name: 'app_reservationa_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservationa_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservationa/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }
    #[Route('/{idRes}', name: 'app_reservationa_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getIdRes(), $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservationa_index', [], Response::HTTP_SEE_OTHER);
    }
}
