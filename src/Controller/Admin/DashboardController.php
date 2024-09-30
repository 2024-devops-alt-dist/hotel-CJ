<?php

namespace App\Controller\Admin;

use App\Entity\Chambre;
use App\Entity\Hotel;
use App\Entity\Picture;
use App\Entity\Reservation;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;


use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(HotelCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img src="/assets2/images/logo_ts.png">');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Site Web', 'fa fa-home', 'route_name');
    
        yield MenuItem::section('Gestion User');
        yield MenuItem::subMenu('Utilisateurs', 'fa fa-users')->setSubItems([
            MenuItem::linkToCrud('Employés', 'fa fa-tags', User::class),
            MenuItem::linkToCrud('Clients', 'fa fa-file-text', User::class),
        ]);

        yield MenuItem::section('Gestion Patrimoine');
        yield MenuItem::linkToCrud('Hotel', 'fa-solid fa-hotel', Hotel::class);
        yield MenuItem::linkToCrud('Chambres', 'fa-solid fa-bed', Chambre::class);
        yield MenuItem::linkToCrud('Images', 'fa-solid fa-image', Picture::class); 

        yield MenuItem::section('Les réservations');
        yield MenuItem::linkToCrud('Réservation', 'fa-solid fa-calendar-days', Reservation::class);
    }
}

        // yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

 // yield MenuItem::linkToUrl('Notre site web', null, '/');
        // yield MenuItem::linkToUrl('Search in Google', 'fab fa-google', 'https://google.com');
        // yield MenuItem::linkToCrud('Utilisateurs', 'fa-solid fa-users', User::class);
        // // Admin and Managers (Gerants)
        // yield MenuItem::linkToCrud('Employés', 'fa-solid fa-users', User::class)
        //     ->setController(UserCrudController::class)
        //     ->setQueryParameter('role', 'ROLE_ADMIN')
        //     ->setQueryParameter('role', 'ROLE_GERANT');

        // // Clients
        // yield MenuItem::linkToCrud('Clients', 'fa-solid fa-users', User::class)
        //     ->setController(UserCrudController::class)
        //     ->setQueryParameter('role', 'ROLE_USER');