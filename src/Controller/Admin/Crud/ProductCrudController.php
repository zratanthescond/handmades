<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Product;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{

    private string $ref;

    public function __construct()
    {
        $this->ref = (new \DateTime())->format("HisdmY");
    }

    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function createEntity(string $entityFqcn)
    {

        return (new Product())->setRef($this->ref);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityManager->persist($entityInstance);

        $entityManager->flush();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(["createdAt" => "DESC"])->setEntityLabelInPlural("Produits")->setEntityLabelInSingular("Produit");
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add("category");
    }

    public function configureFields(string $pageName): iterable
    {

        return [

            ImageField::new("thumbnail", "Image")->setBasePath("/upload/img")->onlyOnIndex(),
            TextField::new("title")->setColumns(6),
            TextField::new("ref", "Référence")->setColumns(4)->setFormTypeOptions(["disabled" => true]),
            TextareaField::new("description")->onlyOnForms()->setColumns(12),
            MoneyField::new("price", "Prix")->setCurrency("TND")->setStoredAsCents(false)->setColumns(4),
            IntegerField::new("qty", "Quantité")->setColumns(4)->formatValue(function ($v, $e) {
                if ($v === 0) {
                    return '<span class="text-danger">En rupture de stock</span>';
                }

                return $v;
            }),
            BooleanField::new("isApprovisionnable", "Approvisionnable")->setColumns(4)->onlyOnForms()->addCssClass("mt-4"),
            AssociationField::new("category", "Catégorie")->setColumns(3),
            AssociationField::new("brand", "Marque")->setColumns(3)->onlyOnForms(),
            AssociationField::new("type")->setColumns(2)->onlyOnForms(),
            // CountryField::new("originCountry", "Pays d'origine")->setColumns(3)->onlyOnForms(),

            FormField::addPanel("Infos"),

            CollectionField::new("infos")->setEntryType(ProductInfoType::class)->onlyOnForms(),

            FormField::addPanel("Images"),
            CollectionField::new("images")->setEntryType(ProductImageType::class)->onlyOnForms(),
            FormField::addPanel("SEO"),
            TextareaField::new("metaDescription", "Meta description")->onlyOnForms(),
            CollectionField::new("users", "Wishlist")->onlyOnIndex()->formatValue(fn($v, $e) => $e->getUsers()->count())
        ];
    }
}
