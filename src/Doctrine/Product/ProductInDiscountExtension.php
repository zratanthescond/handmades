<?php

namespace App\Doctrine\Product;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use Doctrine\ORM\QueryBuilder;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Product;

class ProductInDiscountExtension implements QueryCollectionExtensionInterface
{
    
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?string $operationName = null)
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass)
    {

        if($resourceClass == Product::class) {

            
        }
    }

}