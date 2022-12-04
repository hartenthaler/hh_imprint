<?php

namespace Hartenthaler\Webtrees\Module\Imprint;;

use Composer\Autoload\ClassLoader;

$loader = new ClassLoader();
$loader->addPsr4('Hartenthaler\\Webtrees\\Module\\Imprint\\', __DIR__);
$loader->addPsr4('Hartenthaler\\Webtrees\\Module\\Imprint\\', __DIR__ . '/src');
$loader->register();
