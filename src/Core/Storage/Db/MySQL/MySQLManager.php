<?php
namespace Core\Storage\Db\MySQL;

use Closure;
use App\HNY\HnyModel;
use App\HNY\HnyTestModel;
use Core\Storage\Db\Connection\AbstractConnection;
use stalk\Config\Config;

/**
 * Менеджер моделей база данных MySQL.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
class MySQLManager {
    /**
     * Имя конфигурационного файла.
     */
    const MYSQL_CONFIG_NAME = 'mysql';

    /**
     * @var MySQLDbModel[] Список загруженных моделей.
     */
    private $Models = array();

    /**
     * @var AbstractConnection[] Список активных соединений.
     */
    private $Connections = array();

    /**
     * Деструктор.
     */
    public function __destruct() {
        foreach ($this->Connections as $Connection) {
            $Connection->disconnect();
        }
    }

    /**
     * Возвращает нужное соединение.
     * @todo после перехода на 5.5 сделать приватным
     * @param string $name
     * @return AbstractConnection
     */
    public function getConnection($name) {
        if (!isset($this->Connections[$name])) {
            $config = Config::getInstance()->get(self::MYSQL_CONFIG_NAME, $name);

            $Connection = new MySQLConnection();
            $Connection
                ->setHost($config['host'])
                ->setLogin($config['login'])
                ->setPassword($config['password'])
            ;

            $this->Connections[$name] = $Connection;
        }
        return $this->Connections[$name];
    }

    /**
     * Возвращает модель.
     * @param string $name
     * @param callable $Closure
     * @return MySQLDbModel
     */
    private function getModel($name, Closure $Closure) {
        if (!isset($this->Models[$name])) {
            $this->Models[$name] = $Closure();
        }
        return $this->Models[$name];
    }

    /**
     * Возвращает модель для работы с поздравлениями клиентов на новый год.
     * @todo после перехода на 5.5 использовать $this
     * @return HnyModel
     */
    public function getHnyModel() {
        $self = $this;
        return $this->getModel('hny', function() use ($self) {
            return new HnyModel($self->getConnection('hny'));
        });
    }

    /**
     * Возвращает модель для теста работы с поздравлениями клиентов на новый год.
     * @todo после перехода на 5.5 использовать $this
     * @return HnyTestModel
     */
    public function getHnyTestModel() {
        $self = $this;
        return $this->getModel('hny', function() use ($self) {
            return new HnyTestModel($self->getConnection('hny'));
        });
    }
}
