<?php

namespace App\Controller\Admin;

use App\Entity\Chambre;
use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use Symfony\Bundle\SecurityBundle\Security;

class ChambreCrudController extends AbstractCrudController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getEntityFqcn(): string
    {
        return Chambre::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnIndex(),
            TextField::new('title', 'Nom :'),
            TextEditorField::new('description', 'Description :')
                ->onlyOnIndex(),
            TextareaField::new('description', 'Description :')
                ->onlyOnForms(),
            AssociationField::new('hotel', 'Hôtel'),
            NumberField::new('price', 'Prix/nuit :')
            ->formatValue(function ($value, $entity) {
                return number_format($value, 2, ',', '') . ' €';
            }),
            BooleanField::new('disponible', 'Disponible ?'), 
            AssociationField::new('images', 'Images')
            ->formatValue(function ($value) {
                return implode(', ', array_map(function($image) {
                    // Si le chemin ne contient pas 'uploads/images', on le rajoute
                    $imagePath = (strpos($image->getPath(), 'uploads/images') !== false) 
                        ? $image->getPath() 
                        : '/uploads/images/' . $image->getPath();
                    
                    return sprintf(
                        '<a href="#" class="image-link" data-path="%s">%d</a>', 
                        $imagePath, 
                        $image->getId()
                    );
                }, $value->toArray()));
            })
                ->onlyOnIndex(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->overrideTemplates([
                'crud/index' => 'admin/crud/chambre/modal.html.twig',
            ])
            ->setPageTitle('index', 'Liste des chambres')
            ->setPageTitle('edit', function (Chambre $entity) {
                return 'Modifier Chambre #' . $entity->getId() . '- ' . $entity->getTitle(); 
            })
            ->setPageTitle('new', 'Ajouter une nouvelle chambre')
            ;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        
        // Vérifiez si l'utilisateur est un gérant
        if ($this->security->isGranted('ROLE_GERANT')) {
            // Si c'est un gérant, ne montrer que les chambres associées à son hôtel
            $user = $this->security->getUser();

            // Vérifiez si l'utilisateur est une instance de User
            if ($user instanceof User) {
                // Obtenir l'hôtel associé à l'utilisateur
                $hotel = $user->getHotel();

                // Vérifiez si l'hôtel existe avant d'ajouter la condition
                if ($hotel) {
                    $queryBuilder->andWhere('entity.hotel = :hotel')
                                ->setParameter('hotel', $hotel);
                }
            }
        }

        return $queryBuilder;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);
        $this->addFlash('success', 'La chambre a été ajoutée avec succès.');
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::updateEntity($entityManager, $entityInstance);
        $this->addFlash('success', 'La chambre a été modifiée avec succès.');
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance->getReservations()->isEmpty()) {
            $this->addFlash('danger', 'Impossible de supprimer cette chambre car elle est liée à des réservations.');
            return; 
        }

        // Si pas de réservations = suppression
        parent::deleteEntity($entityManager, $entityInstance);
        $this->addFlash('success', 'La chambre a été supprimée avec succès.');
    }
}
