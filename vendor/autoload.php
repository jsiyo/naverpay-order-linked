<?php
spl_autoload_register(function($class) {
    $PHP    = '.php';
    $PREFIX = 'src\\';

    $file   = str_replace(
        '\\', DIRECTORY_SEPARATOR, $PREFIX . $class . $PHP
     );
    if (file_exists($file) == true) {
        require_once $file;
    }
});
