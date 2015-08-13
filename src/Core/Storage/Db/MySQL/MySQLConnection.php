<?php
namespace Core\Storage\Db\MySQL;

use Exception;
use Core\Storage\Db\Connection\AbstractConnection;
use Core\Storage\Db\Connection\Exception\ConnectionException;
use Core\Storage\Db\Exception\DatabaseNotFoundException;
use Core\Storage\Db\Exception\DuplicateEntryException;
use Core\Storage\Db\Exception\TableNotFoundException;
use stalk\Logger\LoggerFactory;

/**
 * Класс описания соединения с базой данных MySQL.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
class MySQLConnection extends AbstractConnection {
    /**
     * @inheritdoc
     */
    protected $charset = 'utf8';

    /**
     * @inheritdoc
     */
    protected $collate = 'utf8_unicode_ci';

    /**
     * @inheritdoc
     */
    public function connect() {
        if ($this->isConnected()) {
            return;
        }

        $try = 1;

        do {
            try {
                $this->Resource = mysql_connect($this->host, $this->login, $this->password, false);
                break;
            } catch (Exception $Ex) {
                LoggerFactory::getRootLogger()->error($Ex);

                if ($try > 1 && $try < $this->connectionRetries) {
                    // в первый раз не спим, потому что мб сеть затупила и надо сразу попробовать снова
                    // в последний раз не спим, потому что после этого не вызываем mysql_connect()
                    usleep($this->progressiveSleepTime * ($try - 1));
                }
            }
        } while (++$try <= $this->connectionRetries);

        if (!$this->Resource) {
            throw new ConnectionException('Cannot connect to mysql server: ' . $this->host);
        }

        $this->isConnected = true;

        if ($this->charset) {
            if (function_exists('mysql_set_charset')) {
                if (!mysql_set_charset($this->charset, $this->Resource)) {
                    throw new ConnectionException('Cannot set MySQL charset ' . $this->charset . ' for ' . $this->host);
                }
            } else {
                $this->query('SET NAMES ' . $this->charset . ' COLLATE ' . $this->collate);
            }
        }

        if ($this->database) {
            $this->useDatabase($this->database, true);
        }
    }

    /**
     * @inheritdoc
     */
    public function disconnect() {
        if ($this->isConnected || $this->Resource) {
            try {
                if (is_resource($this->Resource)) {
                    mysql_close($this->Resource);
                }
            } catch (Exception $Ex) {
                LoggerFactory::getRootLogger()->warn($Ex);
            }

            $this->Resource     = null;
            $this->isConnected  = false;
        }
    }

    /**
     * Выполняет запрос на использование указанной базы данных.
     * @param string $database
     * @param bool $force
     */
    public function useDatabase($database, $force = false) {
        if (!$this->isConnected()) {
            $this->connect();
        }

        if ($this->database != $database || $force) {
            $this->query('USE `'.$database.'`');
            $this->database = $database;
        }
    }

    /**
     * Бросает исключение для произошедшей ошибки.
     * @param int $errorCode
     * @param string $errorMessage
     * @param string $query
     */
    private function throwException($errorCode, $errorMessage, $query) {
        $message = sprintf('%s from host %s: %s', $errorMessage, $this->host, $query);

        switch ($errorCode) {
            case DuplicateEntryException::ERROR_CODE:
                throw new DuplicateEntryException($message);
            case TableNotFoundException::ERROR_CODE:
                throw new TableNotFoundException($message);
            case DatabaseNotFoundException::ERROR_CODE:
                throw new DatabaseNotFoundException($message);
            default:
                throw new ConnectionException($message, $errorCode);
        }
    }

    /**
     * Внутренний метод реализации запроса к базе данных.
     * @param string $query
     * @return MySQLQueryResult
     * @throws ConnectionException
     */
    protected function sendQuery($query) {
        $Result = mysql_query($query, $this->Resource);

        if (!$Result) {
            $error = mysql_error($this->Resource);
            if ($error) {
                $errorCode = mysql_errno($this->Resource);
                $this->throwException($errorCode, $error, $query);
            } else {
                $this->throwException(null, 'No result returned for query', $query);
            }
        }

        return new MySQLQueryResult($Result, $this->Resource);
    }

    /**
     * @inheritdoc
     */
    public function escapeString($string) {
        if (!$this->isConnected()) {
            $this->connect();
        }

        return mysql_real_escape_string($string, $this->Resource);
    }
}
