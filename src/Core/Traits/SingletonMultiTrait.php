<?php
namespace Core\Traits;

/**
 * Трейт множества синглтонов.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
trait SingletonMultiTrait {
    /**
     * @var static[] Экземпляры сущностей синглтонов.
     */
    private static $Instances = array();

    /**
     * @return static Экземпляр сущности синглтона.
     */
    public static function getInstance() {
        if (!isset(self::$Instances[static::class])) {
            self::$Instances[static::class] = new static();
        }
        return self::$Instances[static::class];
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
