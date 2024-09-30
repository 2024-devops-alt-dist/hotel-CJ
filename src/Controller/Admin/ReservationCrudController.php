<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use Symfony\Bundle\SecurityBundle\Security;

class ReservationCrudController extends AbstractCrudController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public static function getEntityFqcn(): string
    {
        return Reservation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnIndex(),
            TextField::new('user', 'Client :')
                ->hideWhenUpdating()
                ->formatValue(function ($value, $entity) {
                    $user = $entity->getUser();
                    return $user ? $user->getFirstname() . ' ' . $user->getLastname() . ' - #' . $user->getId() : 'No client';
                }),
            DateField::new('date_start', 'Date début séjour :')
                ->setFormat('dd/MM/yyyy'),
            DateField::new('date_end', 'Date fin séjour :')
                ->setFormat('dd/MM/yyyy'),
            CollectionField::new('chambre', 'Recap Resa :')
                ->onlyOnDetail()
                ->formatValue(function ($value, $entity) {
                    $chambres = $entity->getChambre();
                    $details = [];
                    $dateStart = $entity->getDateStart();
                    $dateEnd = $entity->getDateEnd();

                    $numberOfDays = $dateEnd->diff($dateStart)->days;

                    foreach ($chambres as $chambre) {
                        $pricePerDay = number_format($chambre->getPrice(), 2, ',', '') . ' €';
                        $hotelCity = $chambre->getHotel() ? $chambre->getHotel()->getCity() : 'Inconnu';
                        $details[] = '#' . $chambre->getID() . ' - ' . $chambre->getTitle() . ' <br> Prix/Nuit : ' . $pricePerDay . ' <br> Total de jour : ' . $numberOfDays . ' <br> Ville : ' . $hotelCity;
                    }

                    return implode('<br>', $details);
                }),
            NumberField::new('total_price', 'Prix Total séjour :')
                ->formatValue(function ($value, $entity) {
                    return number_format($value, 2, ',', '') . ' €';
                }),
            AssociationField::new('chambre', 'Ville de l\'hôtel :')
                ->onlyOnIndex()
                ->formatValue(function ($value, $entity) {
                    $villes = [];
                    foreach ($entity->getChambre() as $chambre) {
                        $hotel = $chambre->getHotel();
                        if ($hotel) {
                            $villes[] = $hotel->getCity();
                        }
                    }
                    return implode(', ', array_unique($villes));
                }),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
        ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ->remove(Crud::PAGE_INDEX, Action::EDIT)
        ->remove(Crud::PAGE_INDEX, Action::NEW)
        ->remove(Crud::PAGE_DETAIL, Action::EDIT)

    ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Liste des réservations')
            ;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        
        // Vérifiez si l'utilisateur est un gérant
        if ($this->security->isGranted('ROLE_GERANT')) {
            // Récupérer l'utilisateur connecté
            $user = $this->security->getUser();
    
            // Vérifiez si l'utilisateur est bien une instance de User
            if ($user instanceof User) {
                // Obtenir l'hôtel associé à l'utilisateur
                $hotel = $user->getHotel();
    
                // Si l'utilisateur est associé à un hôtel, filtrer les réservations
                if ($hotel) {
                    // Filtrer les réservations associées aux chambres de l'hôtel du gérant
                    $queryBuilder->innerJoin('entity.chambre', 'c')
                                ->andWhere('c.hotel = :hotel') 
                                ->setParameter('hotel', $hotel);
                }
            }
        }
    
        return $queryBuilder;
    }
    

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::deleteEntity($entityManager, $entityInstance);
        $this->addFlash('success', 'La réservation a été supprimée avec succès.');
    }
}
