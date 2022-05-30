<?php

/**
 * Inane: Db
 *
 * Inane Database
 *
 * PHP version 8.1
 *
 * @author Philip Michael Raab<peep@inane.co.za>
 * @package Inane\Stdlib
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 */

declare(strict_types=1);

namespace Inane\Db;

use ArrayObject;

use function implode;
use function in_array;
use function is_int;
use function is_null;
use function serialize;
use const null;

/**
 * Each Concrete Builder corresponds to a specific SQL dialect and may implement
 * the builder steps a little bit differently from the others.
 *
 * This Concrete Builder can build SQL queries compatible with MySQL.
 */
class MysqlQueryBuilder implements SQLQueryBuilderInterface {
    protected ArrayObject $query;

    public function __construct() {
        $this->reset();
    }

    protected function reset(): void {
        // $this->query = new \stdClass();
        $this->query = new ArrayObject(['where' => []], ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * Build a base SELECT query.
     */
    public function select(string $table, array $fields): SQLQueryBuilderInterface {
        $this->query->base = 'SELECT ' . implode(', ', $fields) . ' FROM ' . $table;
        $this->query->type = 'select';

        return $this;
    }

    /**
     * Add a WHERE condition.
     */
    public function where(string $field, string|int $value, string $operator = '='): SQLQueryBuilderInterface {
        if (!in_array($this->query->type, ['select', 'update', 'delete']))
            throw new \Exception('WHERE can only be added to SELECT, UPDATE OR DELETE');

        $quote = is_int($value) ? '' : "'";
        $this->query->where[] = "$field $operator $quote$value$quote";

        return $this;
    }

    /**
     * Add a LIMIT constraint.
     */
    public function limit(int $start, ?int $offset = null): SQLQueryBuilderInterface {
        if (!in_array($this->query->type, ['select']))
            throw new \Exception('LIMIT can only be added to SELECT');

        $this->query->limit = " LIMIT $start";
        if (!is_null($offset)) $this->query->limit .= ", $offset";

        return $this;
    }

    /**
     * Get the final query string.
     */
    public function getSQL(): string {
        $query = $this->query;
        $sql = $query->base;
        if (!empty($query->where))
            $sql .= ' WHERE ' . implode(' AND ', $query->where);

        if ($query->offsetExists('limit'))
            $sql .= $query->limit;

        return $sql . ';';
    }
}
