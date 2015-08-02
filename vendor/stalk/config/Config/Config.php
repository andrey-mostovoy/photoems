<?php
namespace stalk\Config;

use stalk\Config\Exception\ConfigKeyNotFoundException;
use stalk\Config\Exception\ConfigNotFoundException;

/**
 * Класс работы с конфигурационными файлами.
 * Конфигурационный файл должен возвращать массив данных.
 * @author andrey-mostovoy <stalk.4.me@gmail.com>
 */
class Config {
    /**
     * Префикс файлов конфига.
     */
    const CONFIG_PREFIX = '.config.php';

    /**
     * @var Config
     */
    private static $Instance;

    /**
     * @var string Директория с конфиг файлом.
     */
    private $configDir;

    /**
     * Конструктор синглтона.
     */
    private function __construct() {}

    /**
     * @return Config
     */
    public static function getInstance() {
        if (!self::$Instance) {
            self::$Instance = new self();
        }
        return self::$Instance;
    }

    /**
     * Возвращает значение конфига.
     * @param string $configName Имя конфига.
     * @param string $key Ключ в конфиге.
     * @return mixed
     * @throws ConfigKeyNotFoundException
     * @throws ConfigNotFoundException
     */
    public function get($configName, $key = null) {
        $config = $this->load($configName);

        if (!$key) {
            return $config;
        }

        if (!isset($config[$key])) {
            throw new ConfigKeyNotFoundException('Config key ' . $key . ' in ' . $configName . ' not found');
        }

        return $config[$key];
    }

    /**
     * Загружает указанный файл конфигурации.
     * @param string $configName
     * @return array
     * @throws ConfigNotFoundException
     */
    private function load($configName) {
        $fileName = $this->configDir . $configName . self::CONFIG_PREFIX;
        if (!file_exists($fileName)) {
            throw new ConfigNotFoundException('Config ' . $configName . ' not found');
        }
        return include $fileName;
    }

    /**
     * Устанавливает директорию, содержащую конфиги.
     * @param string $configDir Путь к дирректории. Окончание должны быть '/'
     * @return Config
     */
    public function setConfigDir($configDir) {
        $this->configDir = $configDir;

        return $this;
    }
}
