<?php

ini_set('display_errors', 1);

error_reporting(E_ALL);

define('ROOT', dirname(__FILE__));
require_once ROOT . '/components/Autoload.php';

$parser = new Parser();
$parser->run($argc, $argv);

if (php_sapi_name() != 'cli') {
    throw new Exception('This application must be run on the command line.');
}
