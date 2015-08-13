<?php
namespace Core\Service;

use Exception;
use Core\Service\Exception\ServiceException;

/**
 * Основной класс для аякс запросов
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
abstract class AbstractServicePage {
    /**
     * Ответ отдавать как для jsonp
     * @var bool
     */
    protected $useJsonp = false;

    /**
     * @var string имя выполняемой команды
     */
    private $commandName;

    /**
     * Результат выполнения комманды
     * @var bool|string|array|int
     */
    private $commandResult = false;

    /**
     * Основной метод выполняемый при запуске
     */
    final public function run() {
        if (!method_exists($this, $this->commandName)) {
            throw new ServiceException('method ' . $this->commandName . ' not found in ' . get_called_class());
        }

        $this->commandResult = $this->{$this->commandName}();
    }

    /**
     * @param string $commandName
     * @return AbstractServicePage
     */
    public function setCommandName($commandName) {
        $this->commandName = $commandName;

        return $this;
    }

    /**
     * Возвращает параметр из поста
     * @param string $key ключ в пост запросе
     * @param mixed $default значение по умолчанию
     * @return mixed
     */
    protected final function get($key, $default = null) {
        if (!isset($_POST[$key])) {
            return $default;
        }
        return $_POST[$key];
    }

    /**
     * Рендер ответа
     * @param string $response json кодированная строка
     */
    private function display($response) {
        if ($this->useJsonp) {
            header('Content-type: text/javascript');
        } else {
            header('Content-type: application/json');
        }

        echo $response;
    }

    /**
     * Ответ сервисера с удачно выполненной командой
     */
    public final function successResponse() {
        $this->display(json_encode(array(
            'result' => $this->commandResult,
        )));
    }

    /**
     * Ответ от сервисера с ошибкой
     * @param Exception $Ex
     */
    public final function errorResponse(Exception $Ex) {
        $this->display(json_encode(array(
            'error' => $Ex->getMessage(),
        )));
    }
}
