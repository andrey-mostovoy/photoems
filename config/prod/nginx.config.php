<?php
/**
 * Nginx конфиг для прод платформы.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
return [
    'listen' => '80',
    'serverNames' => [
        'muzpics.com',
    ],
    'fastcgi' => 'unix:/var/run/php5-fpm.sock',
];
