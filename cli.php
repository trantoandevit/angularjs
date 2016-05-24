<?php

define('BASE_DIR', __DIR__);

require_once BASE_DIR . '/Libs/Fn.php';

//autoload
require_once BASE_DIR . '/Libs/autoload.php';

$uri = explode('::', $argv[1], 2);
$class = $uri[0];
$method = $uri[1];

$instance = new $class;
$instance->{$method}($argv);


