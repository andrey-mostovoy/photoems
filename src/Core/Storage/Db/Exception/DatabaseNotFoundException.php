<?php
namespace Core\Storage\Db\Exception;

use Exception;
use Core\Storage\Db\Connection\Exception\ConnectionException;

/**
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
class DatabaseNotFoundException extends ConnectionException {
    /**
     * Код ошибки.
     */
    const ERROR_CODE = 1049;

    /**
     * Конструктор.
     * @param string $message
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($message = "", $code = self::ERROR_CODE, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
