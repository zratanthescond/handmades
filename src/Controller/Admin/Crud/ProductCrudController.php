<?php

namespace App\Controller\Admin\Crud;

use App\Admin\Filter\EmptyCollectionFilter;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NullFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

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
        return $filters->add("category", "Catégorie")
            ->add("brand", "Marque")
            ->add("type")
            ->add(NullFilter::new("price", "Prix")->setChoiceLabels("Sans Prix", "Avec prix"))
            ->add(NullFilter::new("qty", "Quantité")->setChoiceLabels("Sans quantité", "Avec quantité"))
            ->add(EmptyCollectionFilter::new("images", "Avec images"))
            ->add(TextFilter::new("description"))
            ->add(TextFilter::new("ref", "Référence"));
    }


    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::BATCH_DELETE)
            ->update(Crud::PAGE_INDEX, Action::DELETE, function ($dto) {

                return $dto->displayIf(static function (Product $entity) {

                    return $entity->getProductOrders()->isEmpty();
                });
            });
    }

    public function configureFields(string $pageName): iterable
    {

        return [

            ImageField::new("thumbnail", "Image")->setBasePath("/upload/img")->onlyOnIndex(),
            TextField::new("title", "Nom")->setColumns(6),
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
            CollectionField::new("users", "Wishlist")->onlyOnIndex()->formatValue(fn ($v, Product $e) => $e->getUsers()->count()),
            CollectionField::new("productOrders", "Commandes")->onlyOnIndex()->formatValue(fn ($v, Product $e) => $e->getProductOrders()->count())
        ];
    }
}
