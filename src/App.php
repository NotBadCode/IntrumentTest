<?php

namespace src;

use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use src\service\auth\AuthService;
use src\service\order\OrderService;
use src\service\product\ProductService;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;


/**
 * Class App
 * @package src
 */
class App
{
    /** @var array */
    protected $config = [];

    /** @var RouteCollection */
    protected $routes;

    /** @var Request */
    protected $request;

    /** @var EventDispatcher */
    protected $dispatcher;

    /** @var HttpKernel */
    protected $kernel;

    /** @var EntityManager */
    protected $entityManager;

    /** @var ContainerBuilder */
    protected $container;

    /**
     * App constructor.
     * @param array         $config
     * @param EntityManager $entityManager
     * @throws \Exception
     */
    public function __construct(array $config, EntityManager $entityManager)
    {
        $this->config        = $config;
        $this->entityManager = $entityManager;

        $this->request = Request::createFromGlobals();

        $this->initDependency();

        $this->initRoutes();
    }

    /**
     * @throws \Exception
     */
    protected function initRoutes(): void
    {
        if (!isset($this->config['routes'])) {
            throw new \Exception('Undefined routes config');
        }

        $this->routes = new RouteCollection();

        foreach ($this->config['routes'] as $alias => $route) {
            $alias = \trim($alias);
            if (empty($route['path'])) {
                throw new \Exception("Undefined route's {$alias} path");
            }
            if (empty($route['class'])) {
                throw new \Exception("Undefined route's {$alias} class");
            }
            if (empty($route['action'])) {
                throw new \Exception("Undefined route's {$alias} action");
            }

            $path   = \trim($route['path']);
            $class  = \trim($route['class']);
            $action = \trim($route['action']);

            $controller = new $class($this->container);

            $newRoute = new Route(
                $path,
                ['_controller' => [$controller, $action]],
                $route['requirements'] ?? [],
                $route['options'] ?? [],
                $route['host'] ?? null,
                $route['schemas'] ?? [],
                $route['methods'] ?? [],
                $route['condition'] ?? null
            );

            $this->routes->add($alias, $newRoute);
        }

        $this->container->register('matcher', UrlMatcher::class)
            ->setArguments([$this->routes, new Reference('context')]);
    }

    /**
     *
     */
    protected function initDependency()
    {
        $this->container = new ContainerBuilder();

        $this->container->register('auth', AuthService::class);
        $this->container->register('guzzel', Client::class);

        $this->container->register('product', ProductService::class)
            ->addArgument($this->entityManager);
        $this->container->register('order', OrderService::class)
            ->addArgument($this->entityManager)
            ->addArgument(new Reference('guzzel'));


        $this->container->register('context', RequestContext::class);

        $this->container->register('request_stack', RequestStack::class);
        $this->container->register('controller_resolver', ControllerResolver::class);
        $this->container->register('argument_resolver', ArgumentResolver::class);

        $this->container->register('listener.router', RouterListener::class)
            ->setArguments([new Reference('matcher'), new Reference('request_stack')]);
        $this->container->register('listener.response', ResponseListener::class)
            ->setArguments(['UTF-8']);

        $this->container->register('dispatcher', EventDispatcher::class)
            ->addMethodCall('addSubscriber', [new Reference('listener.router')])
            ->addMethodCall('addSubscriber', [new Reference('listener.response')]);

        $this->container->register('kernel', HttpKernel::class)
            ->setArguments([
                               new Reference('dispatcher'),
                               new Reference('controller_resolver'),
                               new Reference('request_stack'),
                               new Reference('argument_resolver'),
                           ]);
    }

    /**
     * @throws \Exception
     */
    public function run(): void
    {
        $kernel = $this->container->get('kernel');

        try {
            $response = $kernel->handle($this->request);
        } catch (HttpException $exception) {
            $status = $exception->getStatusCode();

            $response = new Response("Error {$status}, {$exception->getMessage()}", $status, $exception->getHeaders());
        }

        $response->send();

        $kernel->terminate($this->request, $response);
    }

    /**
     * @return ContainerBuilder
     */
    public function getContainer(): ContainerBuilder
    {
        return $this->container;
    }
}