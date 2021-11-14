<?php

namespace App\Controller\Admin\Crud;

use App\Core\Security\Permission\UserRoles;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminCrudController extends AbstractCrudController
{

    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;

    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function createEntity(string $entityFqcn)
    {
        return (new User())->setRoles([UserRoles::ADMIN]);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {

        $roles = $entityInstance->getRoles();

        // Each muser must have ROLE_ADMIN to access to dahboard

        if (!in_array(UserRoles::ADMIN, $roles)) {

            array_push($roles, UserRoles::ADMIN);
        }

        $entityInstance->setRoles($roles);

        // if new user

        if ($entityInstance->getPlainPassword()) {

            $hashed = $this->hasher->hashPassword($entityInstance, $entityInstance->getPlainPassword());

            $entityInstance->setPassword($hashed);

            $entityInstance->eraseCredentials();
        }

        $entityManager->persist($entityInstance);

        $entityManager->flush();
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {

        $roles = $entityInstance->getRoles();

        // Each muser must have ROLE_ADMIN to access to dahboard

        if (!in_array(UserRoles::ADMIN, $roles)) {

            array_push($roles, UserRoles::ADMIN);
        }

        $entityInstance->setRoles($roles);

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $response = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $role = "ADMIN";

        $response->andWhere('JSON_CONTAINS(entity.roles, :role) = 1')
            ->setParameter('role', '"ROLE_' . $role . '"');

        return $response;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural("Users")
        ->setEntityLabelInSingular("User")
        ->setEntityPermission(UserRoles::SUPER_ADMIN);
    }

    public function configureFields(string $pageName): iterable
    {

        return [

            IntegerField::new("id")->onlyOnIndex(),
            EmailField::new("email")->setColumns(6),
            TextField::new("plainPassword", "Mot de passe")->onlyWhenCreating(),
            ChoiceField::new("roles")->setChoices(UserRoles::getRoles())->allowMultipleChoices()->setColumns(12),
            TextField::new("fullName", "Nom et prénom")->onlyOnIndex(),
            TextField::new("firstName", "Nom")->onlyOnForms()->setColumns(6),
            TextField::new("lastName", "Prénom")->onlyOnForms()->setColumns(6),
            TextField::new("phoneNumber", "Numéro de téléphone")->setColumns(6)

        ];
    }
}
