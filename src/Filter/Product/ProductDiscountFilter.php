<?php

namespace App\Filter\Product;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use Doctrine\ORM\QueryBuilder;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Symfony\Component\PropertyInfo\Type;

final class ProductDiscountFilter extends AbstractContextAwareFilter
{

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?string $operationName = null)
    {


        // otherwise filter is applied to order and page as well
        if (
            !$this->isPropertyEnabled($property, $resourceClass)
        ) {
            return;
        }
       
        $alias = current($queryBuilder->getDQLPart('from'))->getAlias();

        $queryBuilder
            ->innerJoin(sprintf("%s.discount", $alias), "d")
            ->andWhere("d.expireAt is NULL OR d.expireAt > CURRENT_DATE()")
            ->orderBy("d.expireAt", "DESC");
    }

    public function getDescription(string $resourceClass): array
    {
        if (!$this->properties) {
            return [];
        }

        $description = [];
        foreach ($this->properties as $property => $strategy) {
            $description["in_$property"] = [
                'property' => $property,
                'type' => Type::BUILTIN_TYPE_BOOL,
                'required' => false,
                'swagger' => [
                    'description' => 'This filter has no need to have a mapped property',
                    'name' => 'Custom name to use in the Swagger documentation',
                    'type' => 'Will appear below the name in the Swagger documentation',
                ],
            ];
        }

        return $description;
    }
}
