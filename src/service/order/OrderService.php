<?php

namespace src\service\order;

use Doctrine\ORM\EntityManager;
use src\entity\Order;
use src\entity\Product;
use src\service\product\ProductFactory;
use GuzzleHttp\Client;

/**
 * Class OrderService
 * @package src\service\product
 */
class OrderService
{
    public const CHECK_URL = 'ya.ru';

    /** @var EntityManager */
    protected $entityManager;

    /** @var array */
    protected $errors = [];

    /** @var Client */
    protected $guzzel;

    /**
     * OrderService constructor.
     * @param EntityManager $entityManager
     * @param Client        $guzzel
     */
    public function __construct(EntityManager $entityManager, Client $guzzel)
    {
        $this->entityManager = $entityManager;
        $this->guzzel        = $guzzel;
    }

    /**
     * @param array $productIds
     * @return Order|null
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createNewOrder(array $productIds): ?Order
    {
        $productRepository = $this->entityManager->getRepository(Product::class);

        /** @var Product[] $products */
        $products = $productRepository->findBy(['id' => $productIds]);

        if (empty($products)) {
            $this->errors[] = 'Not found products!';
            return null;
        }

        $order = new Order();
        $order->setStatus(Order::STATUS_NEW);
        $sumTotal = 0;

        foreach ($products as $product) {
            $order->addProduct($product);

            $sumTotal += $product->getPrice();
        }

        $order->setSumTotal($sumTotal);

        $this->entityManager->persist($order);
        $this->entityManager->flush();
        $this->entityManager->clear();

        return $order;
    }

    /**
     * @param int $id
     * @param     $sumTotal
     * @return Order|null
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function payOrder(int $id, $sumTotal): ?Order
    {
        $orderRepository = $this->entityManager->getRepository(Order::class);

        /** @var Order $order */
        $order = $orderRepository->findOneBy(['id' => $id, 'sumTotal' => $sumTotal, 'status' => Order::STATUS_NEW]);

        if (!$order) {
            $this->errors[] = 'Not found order!';
            return null;
        }

        if ($this->checkOrder()) {
            $order->setStatus(Order::STATUS_PAYED);
            $this->entityManager->persist($order);
            $this->entityManager->flush();
            $this->entityManager->clear();
        }

        return $order;
    }

    /**
     * @return bool
     */
    protected function checkOrder(): bool
    {
        $response = $this->guzzel->request('GET', self::CHECK_URL);

        return $response->getStatusCode() === 200;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return string|null
     */
    public function getFirstError(): ?string
    {
        if (empty($this->errors[0])) {
            return null;
        }

        return $this->errors[0];
    }
}