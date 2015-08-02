<?php
namespace Core\Storage;

use Core\Storage\Db\MySQL\MySQLManager;

/**
 * Менеджер стореджей.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
class StorageManager {
    /**
     * @var StorageManager
     */
    private static $Instance;

    /**
     * @var MySQLManager Менеджер стореджа базы данных MySQL.
     */
    private $MySQLManager;

    /**
     * Конструктор синглтона.
     */
    private function __construct() {}

    /**
     * Клонирование объекта запрещаем.
     */
    private function __clone() {}
    
    /**
     * @return StorageManager
     */
    public static function getInstance() {
        if (!self::$Instance) {
            self::$Instance = new self();
        }
        return self::$Instance;
    }

    /**
     * Возвращает менеджер стореджа базы данных MySQL.
     * @return MySQLManager
     */
    public function getMySQLManager() {
        if (!isset($this->MySQLManager)) {
            $this->MySQLManager = new MySQLManager();
        }
        return $this->MySQLManager;
    }
}
