<?php
namespace Core\Traits;

/**
 * Трейт синглтона.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
trait SingletonTrait {
    /**
     * @var static Экземпляр сущности синглтона.
     */
    private static $Instance;

    /**
     * @return static Экземпляр сущности синглтона.
     */
    public static function getInstance() {
        if (!self::$Instance) {
            self::$Instance = new static();
        }
        return self::$Instance;
    }

    /**
     * Конструктор синглтона.
     */
    protected function __construct() {}

    /**
     * Метод клонирования запрещен для синглтона.
     */
    private function __clone() {}
}
