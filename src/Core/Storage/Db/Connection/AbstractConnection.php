<?php
namespace Core\Storage\Db\Connection;

use Core\Storage\Db\QueryResult\AbstractQueryResult;

/**
 * Класс описания абстрактного соединения с базой данных.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
abstract class AbstractConnection {
    /**
     * @var bool Признак наличия соединения.
     */
    protected $isConnected = false;

    /**
     * @var resource Ресурс соединения с базой данных.
     */
    protected $Resource;

    /**
     * @var string Хост инстанса.
     */
    protected $host;

    /**
     * @var string Логин для инстанса.
     */
    protected $login;

    /**
     * @var string Пароль для инстанса.
     */
    protected $password;

    /**
     * @var string Кодировка.
     */
    protected $charset;

    /**
     * @var string Кодировка.
     */
    protected $collate;

    /**
     * @var string Используемая база данных.
     */
    protected $database;

    /**
     * @var int Количество попыток соединения.
     */
    protected $connectionRetries = 3;

    /**
     * @var int Интервал ожидания между попытками соединения.
     */
    protected $progressiveSleepTime = 10000;

    /**
     * Устанавливает хост инстанса.
     * @param string $host
     * @return $this
     */
    public function setHost($host) {
        $this->host = $host;
        return $this;
    }

    /**
     * Устанавливает логин для инстанса.
     * @param string $login
     * @return $this
     */
    public function setLogin($login) {
        $this->login = $login;
        return $this;
    }

    /**
     * Устанавливает пароль для инстанса.
     * @param string $password
     * @return $this
     */
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    /**
     * Устанавливает кодировку.
     * @param string $charset
     * @return $this
     */
    public function setCharset($charset) {
        $this->charset = $charset;
        return $this;
    }

    /**
     * Устанавливает базу данных.
     * @param string $database
     */
    public function setDatabase($database) {
        $this->database = $database;
    }

    /**
     * Выполняет соединение с базой данных.
     */
    abstract public function connect();

    /**
     * Выполняет отключение соединения с базой даных.
     */
    abstract public function disconnect();

    /**
     * Возвращает флаг наличия соединения.
     * @return bool
     */
    public function isConnected() {
        return $this->isConnected && $this->Resource;
    }

    /**
     * Выполняет запрос к базе данных.
     * @param string $query
     * @return AbstractQueryResult
     */
    public function query($query) {
        if (!$this->isConnected()) {
            $this->connect();
        }

        return $this->sendQuery($query);
    }

    /**
     * Внутренний метод реализации запроса к базе данных.
     * @param string $query
     * @return AbstractQueryResult
     */
    abstract protected function sendQuery($query);

    /**
     * Возвращает экранированную строку для базы данных.
     * @param string $string
     * @return string
     */
    abstract public function escapeString($string);

    /**
     * Возвращает кодировку.
     * @return string
     */
    public function getCharset() {
        return $this->charset;
    }

    /**
     * Возвращает кодировку.
     * @return string
     */
    public function getCollate() {
        return $this->collate;
    }
}
