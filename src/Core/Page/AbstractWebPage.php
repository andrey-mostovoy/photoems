<?php
namespace Core\Page;

use Core\Page\Exception\WebException;
use stdClass;

/**
 * Основной класс веб страницы.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
abstract class AbstractWebPage {
    /**
     * @var array Массив аргументов, получаемые от nginx.
     */
    protected $args = array();

    /**
     * @var string Лейоут страницы.
     */
    protected $layout = 'main';

    /**
     * @var stdClass Объект данных для страницы.
     */
    private $PageData;

    /**
     * Конструктор веб страницы.
     * Для чего-либо в дочерних классах использовать метод run.
     */
    public function __construct() {
        // @todo мб стоит завести нечто, что будет с автокомплитом в самих шаблонах.
        $this->PageData = new stdClass();
    }

    /**
     * Биндит пути файлов для подключения на странице.
     * @throws WebException
     */
    protected function bindPagePath() {
        $pageName = $this->getPageName();
        $this->bind('headerMain', HEADER_TPL_DIR . 'Main' . TPL_FILE_EXT);
        if (file_exists($headerPath = HEADER_TPL_DIR . $pageName . TPL_FILE_EXT)) {
            $this->bind('header', $headerPath);
        }
        if (file_exists($pagePath = PAGE_TPL_DIR . $pageName . TPL_FILE_EXT)) {
            $this->bind('content', $pagePath);
        } else {
            throw new WebException('No page tpl for ' . $pageName);
        }
    }

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
     * Возвращает имя страницы.
     * @return string
     */
    protected final function getPageName() {
        return end(explode('\\', get_called_class()));
    }

    /**
     * Основной метод выполняемый при запуске страницы.
     * @return bool true пропустит дальше на рендер страницы, false - нет
     */
    abstract public function run();

    /**
     * Отрисовка страницы. Никаких шаблонов.
     * @return bool
     */
    public final function display() {
        $this->bindPagePath();
        if (!isset($this->PageData->{'jsData'})) {
            $this->PageData->{'jsData'} = array();
        }
        $this->PageData->{'jsData'} = json_encode($this->PageData->{'jsData'});

        /**
         * @var stdClass $PageData данные для страницы
         */
        $PageData = $this->PageData;
        require_once (LAYOUT_DIR . $this->layout . TPL_FILE_EXT);
        return true;
    }

    /**
     * Биндит данные для страницы.
     * @param string $name
     * @param mixed $data
     * @return $this
     */
    protected final function bind($name, $data) {
        $this->PageData->{$name} = $data;
        return $this;
    }

    /**
     * Биндит данные на страницу для js.
     * @param string $name
     * @param mixed $data
     * @return $this
     */
    protected final function addToJs($name, $data) {
        $this->PageData->{'jsData'}[$name] = $data;
        return $this;
    }
}
