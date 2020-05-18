<?php

require dirname(__DIR__) . '/config/bootstrap.php';

use src\App;

$app = new App($config, $entityManager);

$app->run();