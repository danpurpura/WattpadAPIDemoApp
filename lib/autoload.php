<?php

spl_autoload_register(
    function($className) {
        if (!empty($className)) {
            $filepath = __DIR__.'/'.str_replace('\\', '/', $className).'.php';

            if (is_file($filepath)) {
                require($filepath);
            }
        }
    }
);
