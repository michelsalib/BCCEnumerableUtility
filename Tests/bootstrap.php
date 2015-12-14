<?php

/**
 * @var $loader \Composer\Autoload\ClassLoader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

$loader->addPsr4("BCC\\EnumerableUtility\\Tests\\", __DIR__);


return $loader;