<?php
/**
 * Запуск ответа на аякс запрос.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */

use Core\Service\Exception\ServiceException;
use Core\Service\AbstractServicePage;
use stalk\Logger\LoggerFactory;

include_once 'boot.php';

if (isset($_POST['_service'])) {
    $serviceName = str_replace('.', '\\', $_POST['_service']);
    $commandName = $_POST['_command'];

    /**
     * @var AbstractServicePage $Service
     */
    $Service = new $serviceName();
    $Service->setCommandName($commandName);

    try {
        $Service->run();
        $Service->successResponse();
    } catch (ServiceException $Ex) {
        LoggerFactory::getRootLogger()->error($Ex);
        $Service->errorResponse($Ex);
    }
}
