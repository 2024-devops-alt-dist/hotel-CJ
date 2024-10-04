<?php

namespace App\Controller;

use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReservationController extends AbstractController
{
    #[Route('/myReservation', name: 'app_my_reservation')]
    public function index(ReservationRepository $reservationRepository): Response
    {
        $user = $this->getUser();
        $reservations = $reservationRepository->findBy(['user' => $user]);

        return $this->render('reservation/myReservation.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/cancel-reservation/{id}', name: 'app_reservation_cancel')]
    public function cancelReservation(int $id, ReservationRepository $reservationRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $reservation = $reservationRepository->find($id);

        if (!$reservation || $reservation->getUser() !== $user) {
            throw $this->createNotFoundException('Réservation non trouvée.');
        }

        // Vérification de la date d'annulation
        $currentDate = new \DateTime();
        if ($reservation->getDateStart() > (clone $currentDate)->modify('+3 days')) {

            // Gérer l'annulation
            $reservation->setCancelled(true);
            $entityManager->flush();

            $this->addFlash('success', 'Votre réservation a été annulée avec succès.');
        }

        return $this->redirectToRoute('app_my_reservation');
    }
}
