<?php

namespace src\controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class IndexController
 * @package src\controller
 */
class IndexController extends AbstractController
{

    /**
     * GET /
     *
     * @return Response
     */
    public function actionIndex()
    {
        return new Response();
    }
}