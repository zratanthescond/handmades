<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MenuBuilderController extends AbstractController
{

    /**
     * @Route("/admin/menu-builder", name="menu_builder")
     */

    public function index()
    {
       
        return $this->render("dashboard/menu_builder.html.twig");
    }
}
