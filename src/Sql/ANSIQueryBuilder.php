<?php

/**
 * Inane: Db
 *
 * Inane Database
 *
 * PHP version 8.1
 *
 * @author Philip Michael Raab<peep@inane.co.za>
 * @package Inane\Db
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
 */

declare(strict_types=1);

namespace Inane\Db\Sql;

use Inane\Stdlib\Options;

use function count;
use function implode;
use function in_array;
use function is_int;
use function is_null;
use const null;

/**
 * Each Concrete Builder corresponds to a specific SQL dialect and may implement
 * the builder steps a little bit differently from the others.
 *
 * This Concrete Builder can build SQL queries compatible with ANSI SQL.
 *
 * @version 1.0.0
 */
class ANSIQueryBuilder implements SQLQueryBuilderInterface {
    /**
     * Stores the various query parts
     *
     * @var \Inane\Stdlib\Options
     */
    protected Options $query;

    public function __construct() {
        $this->reset();
    }

    protected function reset(): void {
        // $this->query = new \stdClass();
        $this->query = new Options(['where' => []]);
    }

    /**
     * Build a base SELECT query.
     *
     * @param string $table source table
     * @param array $fields the fields to show or empty for all
     *
     * @return \Inane\Db\SQLQueryBuilderInterface
     */
    public function select(string $table, array $fields = []): SQLQueryBuilderInterface {
        $fieldsString = count($fields) == 0 ? '*' : implode(', ', $fields);

        $this->query->base = 'SELECT ' . $fieldsString . ' FROM ' . $table;
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
    public function limit(int $limit, ?int $offset = null): SQLQueryBuilderInterface {
        if (!in_array($this->query->type, ['select']))
            throw new \Exception('LIMIT can only be added to SELECT');

        if (!is_null($offset)) $this->query->limit = " OFFSET $offset ROWS";
        $this->query->limit .= " FETCH FIRST $limit ROWS ONLY";

        return $this;
    }

    /**
     * Get the final query string.
     */
    public function getSQL(): string {
        $query = $this->query;
        $sql = $query->base;
        if (!empty($query->where))
            $sql .= ' WHERE ' . implode(' AND ', $query->where->toArray());

        if ($query->offsetExists('limit'))
            $sql .= $query->limit;

        return $sql . ';';
    }
}
