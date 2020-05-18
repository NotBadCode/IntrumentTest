<?php

namespace src\controller;

use src\service\product\ProductService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class ProductController
 * @package src\controller
 */
class ProductController extends AbstractController
{
    public const COUNT_GENERATED_PRODUCTS = 20;

    /** @var ProductService */
    protected $productService;

    /**
     * ProductController constructor.
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function __construct(ContainerBuilder $container)
    {
        parent::__construct($container);

        $this->productService = $container->get('product');
    }

    public function actionIndex()
    {
        return new Response();
    }

    /**
     * GET /product/generate
     *
     * @return Response
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function actionGenerate()
    {
        if (!$this->auth->checkAuthorization()) {
            throw new  AccessDeniedHttpException();
        }

        $products = $this->productService->getProductFactory()->createProducts(self::COUNT_GENERATED_PRODUCTS);

        $result = [];
        foreach ($products as $key => $product) {
            $result[$product->getId()] = [
                'id'    => $product->getId(),
                'name'  => $product->getName(),
                'price' => $product->getPrice(),
            ];
        }

        return new JsonResponse($result);
    }
}