<?php
namespace Core\Storage\Db\QueryTemplate;

use Serializable;
use Core\Storage\Db\Connection\AbstractConnection;
use Core\Template\AbstractBlitzTemplate;

/**
 * Класс для работы с SQL шаблонами через blitz.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
class BlitzQueryTemplate extends AbstractBlitzTemplate {
    /**
     * Объект соединения с базой данных.
     * @var AbstractConnection
     */
    private $Connection;

    /**
     * Устанавливает соединение, в рамках которого будет выполнен запрос из шаблона.
     * @param AbstractConnection $Connection
     */
    public function bindConnection(AbstractConnection $Connection) {
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
     * Значение в виде целого числа.
     * @param mixed $x
     * @return int
     */
    public function i($x) {
        return (int) $x;
    }

    /**
     * Значение для вставки после конструкции WHERE field IN,
     * когда передаётся массив целых чисел.
     * @param mixed[] $a
     * @return string
     */
    public function inint(array $a) {
        return '(' . implode(',', array_map('intval', $a)) . ')';
    }

    /**
     * Значение в виде целого числа или литерал NULL.
     * @param mixed|null $x
     * @return int|string
     */
    public function inul($x) {
        return is_null($x) ? 'NULL' : $this->i($x);
    }

    /**
     * Возвращает строковое перечисление данных.
     * @param array $a массив строковых данных
     * @return string результирующее строкове перечисление запрошенных данных
     */
    public function instr(array $a) {
        $a = array_map(array($this, 's'), $a);
        return '(' . join(',', $a) . ')';
    }

    /**
     * Возвращает строковое представление для SQL.
     * @param string $string
     * @throw LogicException ислкючение
     * @return string
     */
    public function s($string) {
        return '\'' . $this->Connection->escapeString($string) . '\'';
    }

    /**
     * Возвращает представление даты для SQL.
     * @param int $timestamp таймстамп даты, представление которой необходимо создать
     * @return string
     */
    public function d($timestamp) {
        $date = date('Y-m-d', $timestamp);
        return $this->s($date);
    }

    /**
     * Возвращает представление даты со временем для SQL. Точность представления - секунда.
     * @param int $timestamp таймстамп даты, представление которой необходимо создать
     * @return string
     */
    public function dt($timestamp) {
        $date = date('Y-m-d H:i:s', $timestamp);
        return $this->s($date);
    }

    /**
     * @param $float
     * @return float Float-представление
     */
    public function f($float) {
        return (float) $float;
    }

    /**
     * @param object $value
     * @return string
     */
    public function row($value) {
        if ($value instanceof Serializable) {
            return $value->serialize();
        } else {
            return $value;
        }
    }
}
