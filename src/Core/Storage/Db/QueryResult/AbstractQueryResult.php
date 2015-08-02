<?php
namespace Core\Storage\Db\QueryResult;

/**
 * Класс для работы с результатом успешного выполнения запроса.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
abstract class AbstractQueryResult {
    /**
     * @var int Последний вставленный id.
     */
    protected $lastInsertId;

    /**
     * @var int Количество затронутых строк.
     */
    protected $affectedRows;

    /**
     * @var array[] Полный результат выборки.
     */
    protected $result = array();

    /**
     * Возвращает массив значений определенного столбца из выборки.
     * @param string $column
     * @return int[]|string[]
     */
    public function getColumn($column) {
        return array_map(function($row) use($column) {
            return $row[$column];
        }, $this->result);
    }

    /**
     * Возвращает первое заначение, указанного столбца.
     * @param string $column
     * @return string
     */
    public function getValue($column) {
        return $this->result[0][$column];
    }

    /**
     * Возвращает результат в виде массива строк.
     * @return array[]
     */
    public function getAll() {
        return $this->result;
    }

    /**
     * Возвращает массив, в котором ключами являются
     * значения одного столбца из выборки, а значениями - другого.
     * @param string $valueColumn
     * @param string $keyColumn
     * @return array[]
     */
    public function getColumnWithKey($valueColumn, $keyColumn) {
        $result = array();

        foreach ($this->result as $row) {
            $result[$row[$keyColumn]] = $row[$valueColumn];
        }

        return $result;
    }

    /**
     * Возвращает массив, в котором ключами являются
     * значения одного столбца из выборки, а значениями - строки.
     * @param string $keyColumn Столбец ключей
     * @return array[]
     */
    public function getRowsWithKey($keyColumn) {
        $result = array();

        foreach ($this->result as $row) {
            $result[$row[$keyColumn]] = $row;
        }

        return $result;
    }

    /**
     * Возвращает последний вставленный id.
     * @return int
     */
    public function getLastInsertId() {
        return $this->lastInsertId;
    }

    /**
     * Возвращает количество затронутых строк.
     * @return int
     */
    public function getAffectedRows() {
        return $this->affectedRows;
    }
}
