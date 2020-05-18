<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$config = require dirname(__DIR__) . '/config/config.php';

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode                 = true;
$proxyDir                  = null;
$cache                     = null;
$useSimpleAnnotationReader = false;
$configAnnotation          =
    Setup::createAnnotationMetadataConfiguration([dirname(__DIR__) . "/src/entity"], $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);


// obtaining the entity manager
$entityManager = EntityManager::create($config['db'], $configAnnotation);

