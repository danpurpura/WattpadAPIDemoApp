<?php

// basic autoloader that supports namespaces

spl_autoload_register(
    function($className) {
        if (!empty($className)) {
            $filePath = __DIR__.'/'.str_replace('\\', '/', $className).'.php';

            if (is_file($filePath)) {
                require($filePath);
            }
        }
    }
);
