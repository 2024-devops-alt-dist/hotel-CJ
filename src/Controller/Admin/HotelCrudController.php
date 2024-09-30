<?php

namespace App\Controller\Admin;

use App\Entity\Hotel;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\SecurityBundle\Security;

class HotelCrudController extends AbstractCrudController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public static function getEntityFqcn(): string
    {
        return Hotel::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Liste des hôtels')
            ->setPageTitle('edit', function (Hotel $entity) {
                return 'Modifier #' . $entity->getId() . '- ' . $entity->getName(); 
            })
            ->setPageTitle('new', 'Ajouter un nouvel hôtel')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnIndex(),
            AssociationField::new('gerant', 'Nom gérant(e) :')
                ->onlyOnIndex()
                ->formatValue(function ($value, $entity) {
                    if ($entity->getGerant()) {
                        return $entity->getGerant()->getFirstName() . ' ' . $entity->getGerant()->getLastName();
                    }
                    return '';
                }),
            AssociationField::new('gerant', 'Nom gérant(e) :')
                ->onlyOnForms() 
                ->setFormTypeOption('class', User::class)
                ->setFormTypeOption('choice_label', function (User $user) {
                    return (string) $user; 
                })
                ->setFormTypeOption('query_builder', function (UserRepository $repository): QueryBuilder {
                    return $repository->createQueryBuilder('u')
                        ->where('u.roles LIKE :admin OR u.roles LIKE :gerant')
                        ->setParameter('admin', '%ROLE_ADMIN%')
                        ->setParameter('gerant', '%ROLE_GERANT%');
                }),
            TextField::new('name', 'Nom de l\'hôtel:')
                ->setHelp('Veuillez respecter ce format : Hôtel du Clair de Lune NOM_DE_LA_VILLE'),
            TextField::new('address', 'Adresse Postale :'),
            TextField::new('city', 'Ville :'),
            TextEditorField::new('description', 'Description :')
                ->onlyOnIndex(),
            TextareaField::new('description', 'Description :')
                ->onlyOnForms(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {

        $isGerant = $this->security->isGranted('ROLE_GERANT');
        $actionconfig = $actions ;

        if ($isGerant) {
            $actionconfig->remove(Crud::PAGE_INDEX, Action::NEW);
            $actionconfig->remove(Crud::PAGE_INDEX, Action::DELETE);  
            $actionconfig->remove(Crud::PAGE_INDEX, Action::EDIT); 
        }

        return $actionconfig;
    }

    
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);
        $this->addFlash('success', 'L\'hôtel a été ajouté avec succès.');
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::updateEntity($entityManager, $entityInstance);
        $this->addFlash('success', 'L\'hôtel a été modifié avec succès.');
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance->getChambres()->isEmpty()) {
            $this->addFlash('danger', 'Impossible de supprimer un hôtel avec des réservatiosn en cours.');
            return;
        }

        parent::deleteEntity($entityManager, $entityInstance);
        $this->addFlash('success', 'L\'hôtel a été supprimé avec succès.');
    }
    
}
