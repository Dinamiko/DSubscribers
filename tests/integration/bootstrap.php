<?php
declare(strict_types=1);

error_reporting(E_ALL | E_STRICT);

$baseDir = dirname(__FILE__);
$pluginDir = dirname($baseDir, 2);

var_dump($pluginDir);

require_once $pluginDir . '/vendor/autoload.php';

$wpDir = dirname($pluginDir, 3);
require_once $wpDir . '/wp-load.php';
