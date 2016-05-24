<?php

spl_autoload_register(function($class)
{
    $parts = explode('/', BASE_DIR . '/' . str_replace("\\", '/', $class) . '.php');
    $file = '';
    foreach ($parts as $part)
    {
        $file .= ucfirst($part) . '/';
    }
    $file = rtrim($file, '/');

    if (file_exists($file))
    {
        require_once $file;
    }
});
