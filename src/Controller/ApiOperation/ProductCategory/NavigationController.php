<?php

namespace App\Controller\ApiOperation\ProductCategory;

use App\Entity\ProductCategory;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class NavigationController extends AbstractController
{

    public function __invoke()
    {
        $repo = $this->getDoctrine()->getRepository(ProductCategory::class);

        $categories = $repo->findAll();

        $nav = [];

        foreach ($categories as $categorie) {

            $el = [];

            if (!$categorie->getParent()) {

                $el["item"] = $categorie;

                $subcategories = $categorie->getProductCategories();

                if (!$subcategories->isEmpty()) {

                    $el["items"] = [];
                     $el["items"][]=$categorie; 
                     $clone=NULL;
                     $temp=new stdClass();
                    foreach ($subcategories as $subcategory) {
                      // var_dump($subcategory);
                        $subcategoriesChilds = $subcategory->getProductCategories();
                        
                        if (!$subcategoriesChilds->isEmpty()) {
                
                            array_push($subcategory->subItems,['id'=>$subcategory->getId(),'title'=>$subcategory->getTitle()] );
                            foreach ($subcategoriesChilds as $sub) {

                                array_push($subcategory->subItems, $sub);
                               
                            }
                        
                         
                        }
                       
                        array_push($el["items"], $subcategory);
                    }

                    array_push($nav, $el);
                } else {

                    array_push($nav, ["item" => $categorie]);
                }
            }
        }

        return $nav;
    }
}
