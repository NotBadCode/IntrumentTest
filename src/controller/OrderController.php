<?php

namespace src\controller;

use src\service\order\OrderService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class OrderController
 * @package src\controller
 */
class OrderController extends AbstractController
{
    /** @var OrderService */
    protected $orderService;

    /**
     * OrderController constructor.
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function __construct(ContainerBuilder $container)
    {
        parent::__construct($container);

        $this->orderService = $container->get('order');
    }

    /**
     * POST /order/create
     *
     * @return JsonResponse
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function actionCreate()
    {
        if (!$this->auth->checkAuthorization()) {
            throw new  AccessDeniedHttpException();
        }

        $productIds = $this->request->getCurrentRequest()->get('products');

        if (empty($productIds) || !\is_array($productIds)) {
            throw new BadRequestHttpException('Not found products!');
        }

        $order = $this->orderService->createNewOrder($productIds);
        if (!$order) {
            throw new BadRequestHttpException($this->orderService->getFirstError());
        }

        return new JsonResponse([
                                    'id'     => $order->getId(),
                                    'status' => $order->getStatus(),
                                ]);
    }

    /**
     * POST /order/pay
     *
     * @return JsonResponse
     */
    public function actionPay()
    {
        if (!$this->auth->checkAuthorization()) {
            throw new  AccessDeniedHttpException();
        }

        $id = $this->request->getCurrentRequest()->get('id');
        $sumTotal = $this->request->getCurrentRequest()->get('sumTotal');

        if (empty($id) || empty($sumTotal)) {
            throw new BadRequestHttpException('Not found products!');
        }

        $order = $this->orderService->payOrder($id, $sumTotal);
        if (!$order) {
            throw new BadRequestHttpException($this->orderService->getFirstError());
        }

        return new JsonResponse([
                                    'id'     => $order->getId(),
                                    'status' => $order->getStatus(),
                                ]);
    }
}