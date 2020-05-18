<?php

namespace src\service\product;

use Doctrine\ORM\EntityManager;
use src\entity\Product;

/**
 * Class ProductFactory
 * @package src\service\product
 */
class ProductFactory
{
    /** @var EntityManager */
    protected $entityManager;

    /**
     * ProductFactory constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int  $count
     * @param bool $save
     * @return Product[]
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createProducts(int $count, bool $save = true): array
    {
        $products = [];
        for ($i = 0; $i < $count; $i++) {
            $product = $this->createProduct(false);

            if ($save) {
                $this->entityManager->persist($product);
            }

            $products[] = $product;
        }

        if ($save) {
            $this->entityManager->flush();
            $this->entityManager->clear();
        }

        return $products;
    }

    /**
     * @param bool $save
     * @return Product
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function createProduct(bool $save = true): Product
    {
        $product = new Product();

        $randomInt = \random_int(0, 9999);
        $product->setName("Product{$randomInt}");

        $randomInt = \random_int(1, 9999);
        $product->setPrice($randomInt);

        if ($save) {
            $this->entityManager->persist($product);
            $this->entityManager->flush();
            $this->entityManager->clear();
        }

        return $product;
    }
}