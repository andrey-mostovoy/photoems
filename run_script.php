<?php
/**
 * Запуск на выполнение скрипта.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */

use Core\Script\AbstractScript;

include_once 'boot.php';

$argv = $_SERVER['argv'];
$file = array_shift($argv);

if (isset($argv[0])) {
    $scriptName = str_replace('.', '\\', $argv[0]);

    /**
     * @var AbstractScript $Script
     */
    $Script = new $scriptName();
    if (!empty($args)) {
        $Script->setArgs($args);
    }
    $Script->run();
} else {
    echo 'Script name is not set' . PHP_EOL;
}
