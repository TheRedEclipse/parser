<?php

function autoload($class_name)
{
    $array_paths = [
        '/components/',
    ];

    foreach ($array_paths as $path) {
        $path = ROOT . $path . $class_name . '.php';
        if (is_file($path)) {
            include $path;
        }
    }
}

spl_autoload_register('autoload');
