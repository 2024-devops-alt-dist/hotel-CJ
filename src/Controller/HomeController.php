<?php

namespace App\Controller;

use App\Repository\HotelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(HotelRepository $hotelRepository): Response
    {
        $hotels = $hotelRepository->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'hotels' => $hotels,
        ]);
    }

    #[Route('/search', name: 'app_search_hotel', methods: ['GET'])]
    public function searchHotel(Request $request, HotelRepository $hotelRepository): RedirectResponse
    {
        $hotelId = $request->query->get('hotel');
        $startDate = $request->query->get('start_date');
        $endDate = $request->query->get('end_date');
        $guests = $request->query->get('guests');


        if (!$hotelId) {
            return $this->redirectToRoute('app_home');
        }

        return $this->redirectToRoute('app_hotel_detail', [
            'id' => $hotelId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'guests' => $guests,
        ]);
    }
    
}


