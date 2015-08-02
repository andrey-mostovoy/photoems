<?php
namespace Core\Page;

use Core\Page\Exception\WebException;

/**
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
abstract class AbstractApiPage extends AbstractWebPage {
    public function __construct() {
        try {
            parent::__construct();
        } catch (WebException $Ex) {
            // @todo сделать конкретное исключение по отсутсвию шаблона страницы
            // ничего страшного
        }
    }

    protected function getPostBackUrl($transactionId) {}

    protected function handlePost() {}
}
