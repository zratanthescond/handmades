<?php

namespace App\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Core\Helper\Product\PriceHelper;
use App\Core\RewardPoints\RewardPointsManager;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

final class ProductDataProvider implements 
ItemDataProviderInterface, 
RestrictedDataProviderInterface, 
CollectionDataProviderInterface
{

    private $em;

    private $collectionExtensions;

    public function __construct(EntityManagerInterface $em, iterable $collectionExtensions)
    {
        $this->em = $em;

        $this->collectionExtensions = $collectionExtensions;
    }
    
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Product::class;
    }

    public function getItem(string $resourceClass, $id, ?string $operationName = null, array $context = []): ?Product
    {
        $repo = $this->em->getRepository($resourceClass);

        $product = $repo->find($id);

        $rewardPointsManager = new RewardPointsManager($product->getPrice());

        $product->setRewardPoints([
        "points" => $rewardPointsManager->getPoints(),
        "value" => $rewardPointsManager->getValue()
    ]);

        return $product;
    }


    public function getCollection(string $resourceClass, ?string $operationName = null, array $context = [])
    {

        $repository = $this->em->getRepository($resourceClass);
        $queryBuilder = $repository->createQueryBuilder('u');
        $queryNameGenerator = new QueryNameGenerator();
        
        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operationName, $context);
            if ($extension instanceof QueryResultCollectionExtensionInterface && $extension->supportsResult($resourceClass, $operationName)) {
                $result = $extension->getResult($queryBuilder, $resourceClass, $operationName);
                return $result;
            }
        }

        return [];

        return $queryBuilder->getQuery()->getResult();
    }
}