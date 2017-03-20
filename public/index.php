<?php

/**
 * Define some constants
 */
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', realpath(dirname(__DIR__)) . DS);
define('APPDIR', ROOT . 'app' . DS);
define('ROUTEDIR', APPDIR . 'routes' . DS);
define('VENDORDIR', ROOT . 'vendor' . DS);

/**
 * Include autoload file
 */
if (file_exists(VENDORDIR . 'autoload.php')) {
  require_once VENDORDIR . 'autoload.php';
} else {
  die('<pre>Run `composer.phar install` in root dir</pre>');
}

/**
 * Include bootstrap file
 */
require_once APPDIR . 'bootstrap.php';

/**
 * Include all files located in routes directory
 */
foreach(glob(ROUTEDIR . '*.php') as $router) {
  require_once $router;
}

/**
 * Run the application
 */
$app->run();
