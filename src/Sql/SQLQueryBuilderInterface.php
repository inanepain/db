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

use const null;

/**
 * The Builder interface declares a set of methods to assemble an SQL query.
 *
 * All of the construction steps are returning the current builder object to
 * allow chaining: $builder->select(...)->where(...)
 *
 * @version 1.0.0
 */
interface SQLQueryBuilderInterface {
    /**
     * Build a base SELECT query.
     *
     * @param string $table source table
     * @param array $fields the fields to show or empty for all
     *
     * @return \Inane\Db\SQLQueryBuilderInterface
     */
    public function select(string $table, array $fields = []): SQLQueryBuilderInterface;

    /**
     * Add a where clause
     *
     * @param string $field
     * @param string|int $value
     * @param string $operator
     *
     * @return \Inane\Db\SQLQueryBuilderInterface
     */
    public function where(string $field, string|int $value, string $operator = '='): SQLQueryBuilderInterface;

    /**
     * Limit query response
     *
     * @param int $limit
     * @param null|int $offset
     *
     * @return \Inane\Db\SQLQueryBuilderInterface
     */
    public function limit(int $limit, ?int $offset = null): SQLQueryBuilderInterface;

    // +100 other SQL syntax methods...

    public function getSQL(): string;
}
