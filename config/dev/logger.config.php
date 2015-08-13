<?php
/**
 * Конфиг логов.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
use Monolog\Logger;
use stalk\Logger\LogFormatter;

return [
    'handlers' => [
        'stream' => [
            'path'          => LOG_DIR . 'Root.log',
            'level'         => Logger::DEBUG,
            'formatter'     => LogFormatter::class,
            'format'        => "%datetime% - %level_name% %channel% %context%: %message%\n",
            'date_format'   => 'Y-m-d H:i:s',
        ],
    ],
];
