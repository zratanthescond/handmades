<?php

namespace App\Controller\Admin\Crud;

use App\Admin\Filter\EmptyCollectionFilter;
use App\Entity\Product;
use App\Entity\Colors;
use App\Form\ProductDiscountType;
use App\Form\ProductImageType;
use App\Form\ProductInfoType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NullFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class ColorsCrudController extends AbstractCrudController
{

    private string $ref;

    public function __construct()
    {
        $this->ref = (new \DateTime())->format("HisdmY");
    }

    public static function getEntityFqcn(): string
    {
        return Colors::class;
    }

    public function createEntity(string $entityFqcn)
    {

        return (new Colors());
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityManager->persist($entityInstance);

        $entityManager->flush();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(["name" => "DESC"])->setEntityLabelInPlural("Couleurs groupes")->setEntityLabelInSingular("Couleur groupes ");
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add("name", "name");
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::BATCH_DELETE);
    }

    public function configureFields(string $pageName): iterable
    {

        return [


            TextField::new("name", "Couleur groupes")->setColumns(6),
            AssociationField::new('products', 'Produits')->autocomplete()->setColumns(6)->setFormTypeOption('by_reference', false)


        ];
    }
}
