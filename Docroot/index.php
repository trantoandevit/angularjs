<?php

define('BASE_DIR', dirname(__DIR__));

require_once BASE_DIR . '/Libs/Slim/Slim.php';
require_once BASE_DIR . '/Libs/Fn.php';

//autoload
require_once BASE_DIR . '/Libs/autoload.php';

//khoi tao ung dung
$application = new \Libs\Bootstrap();

