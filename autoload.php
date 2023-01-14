<?php

namespace Hartenthaler\Webtrees\Module\LegalNotice;

use Composer\Autoload\ClassLoader;

$loader = new ClassLoader();
$loader->addPsr4('Hartenthaler\\Webtrees\\Module\\LegalNotice\\', __DIR__);
$loader->addPsr4('Hartenthaler\\Webtrees\\Module\\LegalNotice\\', __DIR__ . DIRECTORY_SEPARATOR . 'src');
$loader->register();
