<?php

namespace App\Controller\Admin\Crud;

use App\Core\Security\Permission\UserRoles;
use App\Entity\Promoter;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PromoterCrudController extends AbstractCrudController
{
     
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
      
         $this->passwordEncoder = $passwordEncoder;
    }

    public static function getEntityFqcn(): string
    {
        return Promoter::class;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $plainPassword = $entityInstance->getPlainPassword();

        if($plainPassword) {

            $hashedPassword = $this->passwordEncoder->encodePassword($entityInstance, $plainPassword);

            $entityInstance->setPassword($hashedPassword);

        }

        $entityManager->persist($entityInstance);
        
        $entityManager->flush();

    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityPermission(UserRoles::SUPER_ADMIN);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::BATCH_DELETE)
            ->update(Crud::PAGE_INDEX, Action::DELETE, function ($dto) {

                return $dto->displayIf(static function (Promoter $entity) {

                    return $entity->getDiscountCodes()->isEmpty();
                });
            });
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            TextField::new("fullName", "Nom et prénom")->onlyOnIndex()->setColumns(6),
            TextField::new("lastName", "Nom")->onlyOnForms()->setColumns(6),
            TextField::new("firstName", "Prénom")->onlyOnForms()->setColumns(6),
            TextField::new("alias")->setHelp("Par exemple : Pseudo Instagram. Ceci s'affiche chez le client")->setColumns(6),
            NumberField::new("phoneNumber", "Numéro de téléphone")->setColumns(6),
            EmailField::new("email")->setColumns(6),
            TextField::new("plainPassword", "Mot de passe")
                ->setFormTypeOptions(["attr" => ["minlength" => 6]])
                ->onlyWhenCreating()
                ->setRequired(true)
                ->setColumns(6),
            BooleanField::new("isActif", "Actif")->setColumns(12)
        ];
    }
}
