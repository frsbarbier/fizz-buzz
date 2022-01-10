<?php

// get root source path
$srcPath = dirname(__DIR__);

// call composer autoloader
require $srcPath . '/vendor/autoload.php';

// run application
(new Api\Application($srcPath))->run();
