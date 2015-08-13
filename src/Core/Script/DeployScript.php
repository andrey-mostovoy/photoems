<?php
namespace Core\Script;

use stalk\Logger\LoggerFactory;

/**
 * Скрипт выполняется по окончанию "деплоя".
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
class DeployScript extends AbstractScript {
    /**
     * {@inheritdoc}
     */
    public function run() {
        LoggerFactory::getRootLogger()->info('HELLO from ' . APPLICATION_REVISION);
    }
}
