<?php

return [
    'index' => [
        'path'   => '/',
        'class'  => src\controller\IndexController::class,
        'action' => 'actionIndex',
        'methods' => ['GET']
    ],
    'generate' => [
        'path'   => '/product/generate',
        'class'  => src\controller\ProductController::class,
        'action' => 'actionGenerate',
        'methods' => ['POST']
    ],
    'create' => [
        'path'   => '/order/create',
        'class'  => src\controller\OrderController::class,
        'action' => 'actionCreate',
        'methods' => ['POST']
    ],
    'pay' => [
        'path'   => '/order/pay',
        'class'  => src\controller\OrderController::class,
        'action' => 'actionPay',
        'methods' => ['POST']
    ],
];