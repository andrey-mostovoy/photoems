<?php
/**
 * Nginx конфиг для дев платформы.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
return [
    'listen' => '80',
    'serverNames' => [
        USER . '.photoems.com',
    ],
    'fastcgi' => 'unix:/var/run/php5-fpm.sock',
];
