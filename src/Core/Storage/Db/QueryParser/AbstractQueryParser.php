<?php
namespace Core\Storage\Db\QueryParser;

use Core\Storage\Db\Connection\AbstractConnection;
use Core\Template\AbstractTemplate;

/**
 * Абстрактный парсер запросов к базам данных.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
abstract class AbstractQueryParser {
    /**
     * Объект соединения с базой данных.
     * @var AbstractConnection
     */
    private $Connection;

    /**
     * Конструктор.
     * @param AbstractConnection $Connection
     */
    public function __construct(AbstractConnection $Connection) {
        $this->Connection = $Connection;
    }

    /**
     * Возвращает объект соединения с базой данных,
     * в рамках которого будет выполнен запрос из шаблона
     *
     * @return AbstractConnection
     */
    protected function getConnection() {
        return $this->Connection;
    }

    /**
     * Возвращает шаблонизатор.
     * @return AbstractTemplate
     */
    abstract protected function getTemplate();

    /**
     * Выполняет парсинг запроса, подставляя в него параметры, если есть.
     * @param string $template
     * @param array $params
     * @return string
     */
    public function parse($template, array $params = array()) {
        $Template = $this->getTemplate();
        $Template->load($template);
        $Template->bindConnection($this->getConnection());
        return $Template->parse($params);
    }
}
