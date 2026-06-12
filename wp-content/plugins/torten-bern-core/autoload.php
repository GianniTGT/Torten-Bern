<?php
// Very small PSR-4 autoloader
spl_autoload_register(function($class){
    $prefix = 'TortenBern\\';
    $base = __DIR__ . '/src/';
    $len = strlen($prefix);
    if (strncmp($prefix,$class,$len)!==0) return;
    $relative = substr($class,$len);
    $file = $base . str_replace('\\','/',$relative) . '.php';
    if (file_exists($file)) require $file;
});
