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
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\ProductCategoryRepository;

final class ProductDataProvider implements
    ItemDataProviderInterface,
    RestrictedDataProviderInterface,
    CollectionDataProviderInterface
{

    private $em;

    private $collectionExtensions;
    private $categoryRepository;
    public function __construct(EntityManagerInterface $em, ProductCategoryRepository $categoryRepository, iterable $collectionExtensions)
    {
        $this->em = $em;
        $this->categoryRepository = $categoryRepository;
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
        // dd($product->getColor());
        if ($product->getColor() !== null) {
            $relatedProducts
                = $this->em->getRepository(Product::class)
                ->findByColor($product->getColor()->getId());
            $relatedProducts = array_filter($relatedProducts, function ($p) use ($product) {
                return $p->getId() !== $product->getId();
            });
            // dd($relatedProducts);
            $product->setRelatedProducts(new ArrayCollection($relatedProducts));
        }

        return $product;
    }


    public function getCollection(string $resourceClass, ?string $operationName = null, array $context = [])
    {

        $repository = $this->em->getRepository($resourceClass);
        $queryBuilder = $repository->createQueryBuilder('u');

        $queryNameGenerator = new QueryNameGenerator();

        $categoryId = $context['filters']['category.id'] ?? null;

        if ($categoryId) {

            $category = $this->categoryRepository->find($categoryId);
            if (!$category) {
                throw new \InvalidArgumentException('Category not found');
            }
            $categoryIds = array_merge([$categoryId], $this->categoryRepository->findSubcategoryIds($categoryId));
            // dd($categoryIds);
            $queryBuilder->where('u.category IN (:categories)')
                ->setParameter('categories', $categoryIds);
            unset($context['filters']['category.id']);
        }

        // dd($context);
        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operationName, $context);
            if ($extension instanceof QueryResultCollectionExtensionInterface && $extension->supportsResult($resourceClass, $operationName)) {
                //  dd($queryBuilder->getQuery());
                $result = $extension->getResult($queryBuilder, $resourceClass, $operationName);
                return $result;
            }
        }

        return [];

        return $queryBuilder->getQuery()->getResult();
    }
}
