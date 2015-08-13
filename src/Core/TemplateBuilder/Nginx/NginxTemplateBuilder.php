<?php
namespace Core\TemplateBuilder\Nginx;

use Core\TemplateBuilder\Exception\TemplateBuilderException;
use stalk\Config\Config;
use stalk\Config\Exception\ConfigKeyNotFoundException;

/**
 * Билдер конфига nginx.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
class NginxTemplateBuilder {
    /**
     * Имя конфигурационного файла.
     */
    const CONFIG_NAME = 'nginx';

    /**
     * Папка расположения шаблонов для компиляции.
     */
    const TEMPLATE_DIR = 'nginx/';

    /**
     * Имя файла с готовым конфигурационным файлом.
     */
    const COMPILED_FILE_NAME = 'nginx.app.conf';

    /**
     * Имя файла с шаблоном для компиляции.
     */
    const TEMPLATE_FILE_NAME = 'nginx.template.phtml';

    /**
     * Имя файла с конфигом fastcgi
     */
    const FASTCGI_CONF_FILE_NAME = 'fastcgi.conf';

    /**
     * Имя файла с конфигом для no-cache.
     */
    const NO_CACHE_FILE_NAME = 'no-cache.conf';

    /**
     * Имя файла с конфигом для ssl.
     */
    const SSL_FILE_NAME = 'ssl.conf';

    /**
     * Имя файла с основными локейшинами.
     */
    const CORE_LOCATION_TEMPLATE_FILE_NAME = 'core.locations.phtml';

    /**
     * Имя файла с локейшинами для текущего приложения.
     */
    const VHOST_TEMPLATE_FILE_NAME = 'vhost.phtml';

    /**
     * @var string Строка для прослушивания сервера.
     */
    public $listen;

    /**
     * @var string[] Наименование имен серверов для перехвата.
     */
    public $names = array();

    /**
     * @var string Сокет для связи с FastCGI.
     */
    public $fastcgi = '';

    /**
     * @var string Путь к файлу с конфигом FastCGI.
     */
    public $includeFastCgiConfPath;

    /**
     * @var string Путь к файлу с конфигом для no-cache.
     */
    public $includeNoCachePath;

    /**
     * @var string Путь к файлу с конфигом для ssl соединений.
     */
    public $includeSslPath;

    /**
     * @var string Путь к файлу с основными локейшинами.
     */
    public $coreLocationPath;

    /**
     * @var string Путь к файлу с локейшинами для приложения.
     */
    public $vhostPath;

    /**
     * Конструктор.
     * @throws ConfigKeyNotFoundException
     */
    public function __construct() {
        $this->listen = Config::getInstance()->get(self::CONFIG_NAME, 'listen');
        $this->names = Config::getInstance()->get(self::CONFIG_NAME, 'serverNames');
        $this->fastcgi = Config::getInstance()->get(self::CONFIG_NAME, 'fastcgi');

        $this->includeFastCgiConfPath = $this->getTemplateDir() . self::FASTCGI_CONF_FILE_NAME;
        $this->includeNoCachePath = $this->getTemplateDir() . self::NO_CACHE_FILE_NAME;
        $this->coreLocationPath = $this->getTemplateDir() . self::CORE_LOCATION_TEMPLATE_FILE_NAME;
        $this->vhostPath = $this->getTemplateDir() . self::VHOST_TEMPLATE_FILE_NAME;
        $this->includeSslPath = $this->getTemplateDir() . self::SSL_FILE_NAME;
    }

    /**
     * Возвращает директорию с шаблонами для компиляции.
     * @return string
     */
    protected function getTemplateDir() {
        return TEMPLATE_BUILDER_DIR . self::TEMPLATE_DIR;
    }

    /**
     * @return string Возвращает путь к скомпилированному файлу.
     */
    protected function getCompiledFileName() {
        return ROOT_DIR . '../' . self::COMPILED_FILE_NAME;
    }

    /**
     * @return string Возвращает путь к файлу шаблона для компиляции.
     */
    protected function getTemplateFileName() {
        return $this->getTemplateDir() . self::TEMPLATE_FILE_NAME;
    }

    /**
     * Компилирует конфиг nginx.
     * @throws TemplateBuilderException
     */
    public function compile() {
        $templateFileName = $this->getTemplateFileName();
        if (!file_exists($templateFileName)) {
            throw new TemplateBuilderException('Can not compile nginx from ' . $templateFileName . '. File not found!');
        }

        ob_start();
        include_once $templateFileName;
        $compiled = ob_get_clean();
        file_put_contents($this->getCompiledFileName(), $compiled);
    }
}
