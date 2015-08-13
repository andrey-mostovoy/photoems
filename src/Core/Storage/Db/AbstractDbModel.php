<?php
namespace Core\Storage\Db;

use Core\Storage\AbstractStorageModel;
use Core\Storage\Db\Connection\AbstractConnection;
use Core\Storage\Db\QueryParser\AbstractQueryParser;
use Core\Storage\Db\QueryParser\BlitzQueryParser;
use Core\Storage\Db\QueryResult\AbstractQueryResult;

/**
 * Класс описания абстрактной модели работы с базой данных.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
abstract class AbstractDbModel extends AbstractStorageModel {
    /**
     * @var AbstractConnection Объект соединения с базой данных.
     */
    private $Connection;

    /**
     * @var AbstractQueryParser Объект парсера запросов к базе данных.
     */
    private $QueryParser;

    /**
     * Конструктор.
     * @param AbstractConnection $Connection
     */
    public function __construct(AbstractConnection $Connection) {
        $this->Connection  = $Connection;
        $this->QueryParser = new BlitzQueryParser($Connection);

        $this->Connection->setDatabase($this->getDatabase());
    }

    /**
     * Возвращает название базы данных для модели.
     * @return string
     */
    abstract public function getDatabase();

    /**
     * Возвращает соединение с базой данных.
     * @return AbstractConnection
     */
    protected function getConnection() {
        return $this->Connection;
    }

    /**
     * Возвращает парсер запросов с базой данных.
     * @return AbstractQueryParser
     */
    protected function getQueryParser() {
        return $this->QueryParser;
    }

    /**
     * Возвращает результат выполнения запроса к базе данных.
     * @param string $template
     * @param array $params
     * @return AbstractQueryResult
     */
    protected function query($template, array $params = array()) {
        return $this->getConnection()->query($this->parse($template, $params));
    }

    /**
     * @param string $template
     * @param array $params
     * @return string
     */
    protected function parse($template, array $params = array()) {
        return $this->getQueryParser()->parse($template, $params);
    }
}
