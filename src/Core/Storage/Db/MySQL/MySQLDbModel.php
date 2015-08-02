<?php
namespace Core\Storage\Db\MySQL;

use Serializable;
use Core\Storage\Db\AbstractDbModel;

/**
 * Класс описания модели работы с базой данных MySQL.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
abstract class MySQLDbModel extends AbstractDbModel {
    /**
     * Возвращает имя таблицы к которой привязана модель.
     * @return string
     */
    abstract protected function getTableName();

    /**
     * @inheritdoc
     */
    protected function query($template, array $params = array()) {
        $params['table'] = $this->getTableName();
        return parent::query($template, $params);
    }

    /**
     * Создает базу данных, если ее нет.
     */
    public function createDatabase() {
        /**
         * @var MySQLConnection $Connection
         */
        $Connection = $this->getConnection();
        $Connection->setDatabase('');
        $this->query($this->getCreateDatabaseQuery(), array(
            'db'        => $this->getDatabase(),
            'character' => $this->getConnection()->getCharset(),
            'collate'   => $this->getConnection()->getCollate(),
        ));
        $Connection->useDatabase($this->getDatabase());
    }

    /**
     * Выполняет запрос создания таблицы.
     * @return MySQLQueryResult
     */
    public function createTable() {
        return $this->query($this->getCreateTableQuery(), array(
            'character' => $this->getConnection()->getCharset(),
            'collate'   => $this->getConnection()->getCollate(),
        ));
    }

    /**
     * Выполняет запрос выборки всех данных.
     * @param array $conditions условия для запроса
     * @return MySQLQueryResult
     */
    public function selectAll(array $conditions = array()) {
        return $this->query($this->getSelectAllQuery(), $conditions);
    }

    /**
     * Выполняет запрос добавления данных.
     * @param array $itemData
     * @return MySQLQueryResult
     */
    public function add(array $itemData) {
        return $this->query($this->getInsertQuery(), $itemData);
    }

    /**
     * Выполняет запрос обновления данных.
     * @param array $itemData
     * @return MySQLQueryResult
     */
    public function update(array $itemData) {
        $data = array();
        foreach ($itemData as $field => $value) {
            $data[] = array(
                'field' => $field,
                'value' => $value,
                'need_row' => $value instanceof Serializable,
                'need_int' => is_integer($value),
            );
        }
        return $this->query($this->getUpdateQuery(), array(
            'data' => $data,
            'fields' => $itemData,
        ));
    }

    /**
     * Возвращает данные по ид.
     * @param int $id
     * @return array
     */
    public function selectById($id) {
        $data = $this->select(array('id' => $id))->getAll();
        if (!isset($data[0])) {
            return array();
        }
        return $data[0];
    }

    /**
     * Выполняет запрос удаления данных.
     * @param array $conditions условия для запроса
     * @return MySQLQueryResult
     */
    public function delete(array $conditions) {
        return $this->query($this->getDeleteQuery(), $conditions);
    }

    /**
     * Выполняет запрос выборки данных.
     * @param array $conditions условия для запроса
     * @return MySQLQueryResult
     */
    public function select(array $conditions) {
        return $this->query($this->getSelectQuery(), $conditions);
    }

    /**
     * Выполняет запрос удаления таблицы.
     * @return MySQLQueryResult
     */
    public function dropTable() {
        return $this->query('DROP TABLE {{ row(table) }}');
    }

    /**
     * Возвращает шаблон запроса создания базы данных.
     * @return string
     */
    protected function getCreateDatabaseQuery() {
        return '
            CREATE DATABASE IF NOT EXISTS {{ row(db) }}
            CHARACTER SET {{ row(character) }} COLLATE {{ row(collate) }}
        ';
    }

    /**
     * Возвращает шаблон запроса создания таблицы.
     * @return string
     */
    abstract protected function getCreateTableQuery();

    /**
     * Возвращает шаблон запроса удаления данных.
     * @return string
     */
    abstract protected function getDeleteQuery();

    /**
     * Возвращает шаблон запроса добавления данных.
     * @return string
     */
    abstract protected function getInsertQuery();

    /**
     * Возвращает шаблон запроса обновления данных.
     * @return string
     */
    protected function getUpdateQuery() {
        return '
            UPDATE {{ row(table) }}
            SET
            {{ BEGIN data }}
                {{ IF $need_row }}
                    "{{ $field }}" = {{ row($value) }}
                {{ ELSEIF $need_int }}
                    "{{ $field }}" = {{ i($value) }}
                {{ ELSE }}
                    "{{ $field }}" = {{ s($value) }}
                {{ END }}
                {{ if($_last, \'\', \',\') }}
            {{ END }}
            WHERE "id" = {{ i(fields.id) }}
        ';
    }

    /**
     * Возвращает шаблон запроса выборки всего.
     * @return string
     */
    abstract protected function getSelectAllQuery();

    /**
     * Возвращает шаблон запрос выборки.
     * @return string
     */
    abstract protected function getSelectQuery();
}
