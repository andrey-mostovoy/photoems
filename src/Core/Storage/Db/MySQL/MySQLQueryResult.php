<?php
namespace Core\Storage\Db\MySQL;

use LogicException;
use Core\Storage\Db\QueryResult\AbstractQueryResult;

/**
 * Класс для работы с результатом успешного выполнения запроса к базе MySQL.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
class MySQLQueryResult extends AbstractQueryResult {
    /**
     * Заполняет данные из ресурса результата и соединения
     *
     * @param resource|bool $Result
     * @param resource $ConnectionResource
     * @throws LogicException
     */
    public function __construct($Result, $ConnectionResource) {
        if ((!is_resource($Result) && !is_bool($Result)) || !is_resource($ConnectionResource)) {
            throw new LogicException();
        }

        $this->lastInsertId = mysql_insert_id($ConnectionResource);
        $this->affectedRows = mysql_affected_rows($ConnectionResource);

        if (is_resource($Result)) {
            while ($row = mysql_fetch_assoc($Result)) {
                $this->result[] = $row;
            }
        }
    }

}
