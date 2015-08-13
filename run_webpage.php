<?php
/**
 * Запуск на ответ по запросу обычной страницы.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */

use Core\Page\AbstractWebPage;

include_once 'boot.php';

if (isset($_ENV['page'])) {
    $pageName = $_ENV['page'];
    $args = isset($_ENV['args']) ? $_ENV['args'] : null;

    /**
     * @var AbstractWebPage $Page
     */
    $Page = new $pageName();
    if (!empty($args)) {
        $Page->setArgs($args);
    }
    if ($Page->run()) {
        $Page->display();
    }
}
