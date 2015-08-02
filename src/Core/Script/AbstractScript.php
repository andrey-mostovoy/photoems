<?php
namespace Core\Script;

/**
 * Основной класс веб страницы.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
abstract class AbstractScript {
    /**
     * @var array Массив аргументов, получаемые от nginx.
     */
    protected $args = array();

    /**
     * Конструктор скрипта.
     * Для чего-либо в дочерних классах использовать метод run.
     */
    final public function __construct() {}

    /**
     * Устанавливает аргументы.
     * @param array $args
     */
    public function setArgs($args) {
        $this->args = $args;
    }

    /**
     * Возвращает аргумент, переданный nginx, или указанное дефолтное значение.
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getArg($key, $default = null) {
        if (isset($this->args[$key])) {
            return $this->args[$key];
        }
        return $default;
    }

    /**
     * Возвращает имя скрипта.
     * @return string
     */
    protected final function getPageName() {
        return end(explode('\\', get_called_class()));
    }

    /**
     * Основной метод выполняемый при запуске скрипта.
     */
    abstract public function run();
}
