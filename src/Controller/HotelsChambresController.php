<?php

namespace App\Controller;

use App\Entity\Hotel;
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

        return $this->render('hotels_chambres/details.html.twig', [
            'hotel' => $hotel,
            'hotels' => $hotels,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'guests' => $guests,
        ]);
    }
}
