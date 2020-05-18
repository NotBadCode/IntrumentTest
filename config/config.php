<?php

$config = [];

$config['routes'] = require dirname(__FILE__) . '/routes.php';
$config['db']     = require dirname(__FILE__) . '/db.php';

return $config;