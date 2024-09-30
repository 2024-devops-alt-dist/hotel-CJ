<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    private $passwordHasher;
    private $security;
    private $requestStack;

    public function __construct(UserPasswordHasherInterface $passwordHasher, Security $security, RequestStack $requestStack)
    {
        $this->passwordHasher = $passwordHasher;
        $this->security = $security;
        $this->requestStack = $requestStack;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Liste des utilisateurs')
            ->setPageTitle('edit', function (User $entity) {
                return 'Modifier User #' . $entity->getId() . '- ' . $entity->getFirstname() . ' ' . $entity->getLastname(); 
            })
            ->setPageTitle('new', 'Ajouter un nouvel utilisateur')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnIndex(),
            ChoiceField::new('roles', 'Roles :')
                ->allowMultipleChoices()
                ->renderAsBadges([
                    'ROLE_ADMIN' => 'danger',
                    'ROLE_GERANT' => 'success',
                    'ROLE_USER' => 'primary',
                ])
                ->setChoices([
                    'Admin' => 'ROLE_ADMIN',
                    'Gerant' => 'ROLE_GERANT',
                    'User' => 'ROLE_USER'
                ]),
            TextField::new('firstname', 'Prénom :'),
            TextField::new('lastname', 'Nom de Famille :'),
            EmailField::new('email'),
            TextField::new('password')
                ->onlyWhenCreating(),
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
        if ($entityInstance instanceof User) {
            $entityInstance->setPassword($this->passwordHasher->hashPassword($entityInstance, $entityInstance->getPassword()));
        }

        parent::persistEntity($entityManager, $entityInstance);
        $this->addFlash('success', 'L\'utilisateur a été ajouté avec succès.');
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            if ($entityInstance->getPassword()) {
                $entityInstance->setPassword($this->passwordHasher->hashPassword($entityInstance, $entityInstance->getPassword()));
            }
        }

        parent::updateEntity($entityManager, $entityInstance);
        $this->addFlash('success', 'L\'utilisateur a été modifié avec succès.');
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User && in_array('ROLE_GERANT', $entityInstance->getRoles())) {
            if ($entityInstance->getHotel() !== null) {
                $this->addFlash('danger', 'Vous ne pouvez pas supprimer un gérant qui est associé à un hôtel.');
                return; 
            }
        }

        parent::deleteEntity($entityManager, $entityInstance);
        $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès.');
    }
}
