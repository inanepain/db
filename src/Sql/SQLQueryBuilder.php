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

use function serialize;
use function unserialize;
use const null;

/**
 * SQLQueryBuilder
 *
 * @version 1.0.0
 */
class SQLQueryBuilder implements SQLQueryBuilderInterface {
    /**
     * Stores the various query parts
     *
     * @var \Inane\Stdlib\Options
     */
    protected Options $queryProperties;

    /**
     * SQLQueryBuilder constructor
     *
     * @param string|null $serialised
     */
    public function __construct(?string $serialised = null) {
        $this->reset($serialised);
    }

    /**
     * Start a new query or restore a serialised one.
     *
     * @param string|null $serialised
     *
     * @return void
     */
    protected function reset(?string $serialised = null): void {
        $this->queryProperties = $serialised ? unserialize($serialised) : new Options(['where' => []]);
    }

    /**
     * Create a select query
     *
     * @param string $table
     * @param array $fields
     *
     * @return \Inane\Db\SQLQueryBuilderInterface
     */
    public function select(string $table, array $fields = []): SQLQueryBuilderInterface {
        $this->queryProperties->select = [
            'table' => $table,
            'fields' => $fields
        ];
        $this->queryProperties->type = 'select';

        return $this;
    }

    /**
     * Add a where clause
     *
     * @param string $field
     * @param string|int $value
     * @param string $operator
     *
     * @return \Inane\Db\SQLQueryBuilderInterface
     */
    public function where(string $field, string|int $value, string $operator = '='): SQLQueryBuilderInterface {
        $this->queryProperties->where[] = [
            'field' => $field,
            'value' => $value,
            'operator' => $operator,
        ];

        return $this;
    }

    /**
     * Limit query response
     *
     * @param int $limit
     * @param null|int $offset
     *
     * @return \Inane\Db\SQLQueryBuilderInterface
     */
    public function limit(int $limit, ?int $offset = null): SQLQueryBuilderInterface {
        $this->queryProperties->limit = [
            'limit' => $limit,
            'offset' => $offset,
        ];

        return $this;
    }

    /**
     * Get the serialised query
     *
     * @return string
     */
    public function getSQL(): string {
        return serialize($this->queryProperties);
    }

    /**
     * Get the query as string for specified database type
     *
     * @param \Inane\Db\SQLQueryBuilderInterface $QueryBuilder
     *
     * @return string
     */
    public function getSQLFor(SQLQueryBuilderInterface $QueryBuilder): string {
        $QueryBuilder->select(...$this->queryProperties->select->toArray());

        foreach ($this->queryProperties->where as $where)
            $QueryBuilder->where(...$where);

        $QueryBuilder->limit(...$this->queryProperties->limit->toArray());

        return $QueryBuilder->getSQL();
    }
}
