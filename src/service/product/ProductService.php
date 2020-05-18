<?php

namespace src\service\product;

use Doctrine\ORM\EntityManager;

/**
 * Class ProductService
 * @package src\service\product
 */
class ProductService
{
    /** @var EntityManager */
    protected $entityManager;

    /** @var ProductFactory */
    protected $productFactory;

    /**
     * ProductService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        $this->productFactory = new ProductFactory($this->entityManager);
    }

    /**
     * @return ProductFactory
     */
    public function getProductFactory(): ProductFactory
    {
        return $this->productFactory;
    }
}