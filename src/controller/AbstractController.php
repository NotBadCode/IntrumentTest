<?php

namespace src\controller;

use src\service\auth\AuthService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class AbstractController
 * @package src\controller
 */
abstract class AbstractController
{
    /** @var ContainerBuilder */
    protected $container;

    /** @var RequestStack */
    protected $request;

    /** @var AuthService */
    protected $auth;

    /**
     * AbstractController constructor.
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;

        $this->auth    = $container->get('auth');
        $this->request = $container->get('request_stack');
    }
}