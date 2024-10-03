<?php

namespace App\Controller;

use App\Entity\Hotel;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HotelsChambresController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route(path: '/hotels', name: 'app_hotels_chambres')]
    public function index(): Response
    {
        $hotels = $this->entityManager->getRepository(Hotel::class)->findAll();

        return $this->render('hotels_chambres/index.html.twig', [
            'controller_name' => 'HotelsChambresController',
            'hotels' => $hotels,
        ]);
    }

    #[Route('/hotels/{id}', name: 'app_hotel_detail')]
public function showHotel(Hotel $hotel, Request $request): Response
{
    $hotels = $this->entityManager->getRepository(Hotel::class)->findAll();
    $startDate = $request->query->get('start_date');
    $endDate = $request->query->get('end_date');
    $guests = $request->query->get('guests');

    // Récupérer l'id de l'hôtel sélectionné
    $selectedHotelId = $request->query->get('hotel');
    if ($selectedHotelId) {
        $hotel = $this->entityManager->getRepository(Hotel::class)->find($selectedHotelId);
    }

    // Convertir dates objets DateTime
    $startDateTime = new \DateTime($startDate);
    $endDateTime = new \DateTime($endDate);

    $chambres = $hotel->getChambres();

    // Filtre chbr dispo
    $availableChambres = [];
    foreach ($chambres as $chambre) {
        // Vérif résa chbr
        $reservations = $this->entityManager->getRepository(Reservation::class)->createQueryBuilder('r')
            ->innerJoin('r.chambre', 'c')
            ->where('c.id = :chambreId')
            ->andWhere('r.date_start < :endDate AND r.date_end > :startDate')
            ->setParameter('chambreId', $chambre->getId())
            ->setParameter('startDate', $startDateTime)
            ->setParameter('endDate', $endDateTime)
            ->getQuery()
            ->getResult();

        if (count($reservations) === 0) {
            $availableChambres[] = $chambre;
        }
    }

    return $this->render('hotels_chambres/details.html.twig', [
        'hotel' => $hotel,
        'hotels' => $hotels,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'guests' => $guests,
        'availableChambres' => $availableChambres, 
    ]);
}
}
