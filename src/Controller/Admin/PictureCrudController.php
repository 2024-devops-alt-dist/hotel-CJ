<?php

namespace App\Controller\Admin;

use App\Entity\Picture;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\SecurityBundle\Security;

class PictureCrudController extends AbstractCrudController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getEntityFqcn(): string
    {
        return Picture::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnIndex(),
            TextField::new('path', 'Nom :')
                ->onlyOnIndex(),
            ImageField::new('path', 'Fichier :')
                ->setBasePath('uploads/images') 
                ->setUploadDir('public/uploads/images')
                ->setUploadedFileNamePattern('[slug].[extension]'),
            BooleanField::new('principale', 'Photo principale ?'),
            AssociationField::new('chambres', 'Chambres associées :')
                ->onlyOnIndex()
                ->formatValue(function ($value, $entity) {
                    if ($entity->getChambres()) {
                        return implode(', ', $entity->getChambres()->map(function($chambre) {
                            return $chambre->getTitle(); 
                        })->toArray());
                    }
                    return '';
                }),
            AssociationField::new('chambres', 'Chambres associées :')
                ->onlyOnForms()
                ->setFormTypeOption('by_reference', false)
                ->setFormTypeOption('multiple', true)
                ->setRequired(false),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'DESC'])
            ->setPageTitle('index', 'Photothèque')
            ->setPageTitle('edit', function (Picture $entity) {
                return 'Modifier Image #' . $entity->getId() . '- ' . $entity->getPath(); 
            })
            ->setPageTitle('new', 'Ajouter une nouvelle image')
            ;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        
        // Vérifiez si l'utilisateur est un gérant
        if ($this->security->isGranted('ROLE_GERANT')) {
            // Si c'est un gérant, ne montrer que les images associées aux chambres de son hôtel
            $user = $this->security->getUser();
    
            // Vérifiez si l'utilisateur est une instance de User
            if ($user instanceof User) {
                $hotel = $user->getHotel();
    
                // Vérifiez si l'hôtel existe avant d'ajouter la condition
                if ($hotel) {
                    // Filtrer les chambres associées à l'hôtel
                    $queryBuilder->innerJoin('entity.chambres', 'c')
                                ->andWhere('c.hotel = :hotel') 
                                ->setParameter('hotel', $hotel);
                }
            }
        }
    
        return $queryBuilder;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);
        $this->addFlash('success', 'L\'image a été ajoutée avec succès.');
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::updateEntity($entityManager, $entityInstance);
        $this->addFlash('success', 'L\'image a été modifiée avec succès.');
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::deleteEntity($entityManager, $entityInstance);
        $this->addFlash('success', 'L\'image a été supprimée avec succès.');
    }
}