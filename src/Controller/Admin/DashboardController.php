<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Crud\AdminCrudController;
use App\Controller\Admin\Crud\OutOfStockProductCrudController;
use App\Controller\Admin\Crud\ProductCrudController;
use App\Core\Security\Permission\UserRoles;
use App\Entity\AramexPickUp;
use App\Entity\AramexShipement;
use App\Entity\Blog;
use App\Entity\BlogCategory;
use App\Entity\BlogComment;
use App\Entity\Brand;
use App\Entity\DeliveryType;
use App\Entity\DiscountCode;
use App\Entity\Help;
use App\Entity\Home;
use App\Entity\Order;
use App\Entity\OrderReview;
use App\Entity\Page;
use App\Entity\Parrainage;
use App\Entity\PopUp;
use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Entity\ProductDiscount;
use App\Entity\ProductReview;
use App\Entity\ProductStockSubscription;
use App\Entity\ProductType;
use App\Entity\Promoter;
use App\Entity\SiteInfo;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{

    private $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }
   
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $url = $this->adminUrlGenerator
        ->setController(ProductCrudController::class)
        ->setAction(Action::INDEX)
        ->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Paramall');
    }

    public function configureAssets(): Assets
    {
        return Assets::new()->addWebpackEncoreEntry("app");
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home')->setPermission(UserRoles::SUPER_ADMIN);
        yield MenuItem::linkToCrud('Pages', 'fa fa-files-o', Page::class);
        yield MenuItem::linkToCrud('Homepage', 'fas fa-bookmark', Home::class)
        ->setPermission(UserRoles::SUPER_ADMIN)
        ->setEntityId(1)
        ->setAction(Action::EDIT);

        yield MenuItem::section("Boutique");
        yield MenuItem::linkToCrud('Produits', 'fas fa-list', Product::class);
        yield MenuItem::linkToCrud('Types', 'fas fa-paperclip', ProductType::class);
        yield MenuItem::linkToCrud('Catégories', 'fa fa-folder-open', ProductCategory::class);
        yield MenuItem::linkToCrud('Marques', 'fas fa-cube', Brand::class);


        yield MenuItem::section("Stock Epuisé");

        yield MenuItem::linkToCrud('Notifications', 'fas fa-spinner', ProductStockSubscription::class);

        yield MenuItem::linkToCrud('Produits', 'fa fa-sort-amount-asc', Product::class)->setController(OutOfStockProductCrudController::class);

        
        yield MenuItem::section("Clients")->setPermission(UserRoles::SUPER_ADMIN);

        yield MenuItem::linkToCrud('Clients', 'fas fa-user', User::class)->setPermission(UserRoles::SUPER_ADMIN);

        yield MenuItem::linkToCrud('Parrainages', 'fas fa-users', Parrainage::class)->setPermission(UserRoles::SUPER_ADMIN);
       
       
        yield MenuItem::section("Commandes")->setPermission(UserRoles::SUPER_ADMIN);

        yield MenuItem::linkToCrud('Commandes', 'fas fa-shopping-cart', Order::class)->setPermission(UserRoles::SUPER_ADMIN);

        yield MenuItem::section("Aramex");

        yield MenuItem::linkToCrud('Shipements', 'fas fa-truck', AramexShipement::class);

        yield MenuItem::linkToCrud('Pick Up', 'fas fa-cart-arrow-down', AramexPickUp::class);

        
        yield MenuItem::section("Promotion")->setPermission(UserRoles::SUPER_ADMIN);

        yield MenuItem::linkToCrud('Promotion produits', 'fas fa-magic', ProductDiscount::class)->setPermission(UserRoles::SUPER_ADMIN);

        yield MenuItem::linkToCrud('Code promo', 'fas fa-database', DiscountCode::class)->setPermission(UserRoles::SUPER_ADMIN);

        yield MenuItem::linkToCrud('Promoteurs', 'fas fa-user', Promoter::class)->setPermission(UserRoles::SUPER_ADMIN);
       
        yield MenuItem::section("Blog");

        yield MenuItem::linkToCrud('Articles', 'fas fa-rss', Blog::class);

        yield MenuItem::linkToCrud('Catégories', 'fas fa-th-large', BlogCategory::class);

        yield MenuItem::linkToCrud('Commentaires', 'fas fa-comment', BlogComment::class);

        yield MenuItem::section("Avis");

        yield MenuItem::linkToCrud('Avis produits', 'fas fa-star', ProductReview::class);

        yield MenuItem::linkToCrud('Avis Commandes', 'fas fa-star', OrderReview::class);

        yield MenuItem::section("Help")->setPermission(UserRoles::SUPER_ADMIN);

        yield MenuItem::linkToCrud('Sections', 'fas fa-list', Help::class)->setPermission(UserRoles::SUPER_ADMIN);

        yield MenuItem::section("Paramétres")->setPermission(UserRoles::SUPER_ADMIN);

        yield MenuItem::linkToCrud('Infos', 'fas fa-info', SiteInfo::class)
        ->setPermission(UserRoles::SUPER_ADMIN)
        ->setEntityId(1)
        ->setAction(Action::EDIT);


        yield MenuItem::linkToCrud('PopUp', 'fas fa-image', PopUp::class)
        ->setPermission(UserRoles::SUPER_ADMIN)
        ->setEntityId(1)
        ->setAction(Action::EDIT);

       // yield MenuItem::linkToCrud('Livraison', 'fas fa-truck', DeliveryType::class)->setPermission(UserRoles::SUPER_ADMIN);

       // yield MenuItem::linkToRoute("Menu", "fa fa-sort-amount-asc", "menu_builder")->setPermission(UserRoles::SUPER_ADMIN);

        /// this is for handle user with amdin role

        yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class)->setController(AdminCrudController::class)->setPermission(UserRoles::SUPER_ADMIN);
    }
}
