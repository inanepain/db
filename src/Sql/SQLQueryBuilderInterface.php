<?php

/**
 * Inane: Db
 *
 * Some helpers for database task and query construction.
 *
 * $Id$
 * $Date$
 *
 * PHP version 8.4
 *
 * @author Philip Michael Raab<philip@cathedral.co.za>
 * @package inanepain\db
 * @category db
 *
 * @license UNLICENSE
 * @license https://unlicense.org/UNLICENSE UNLICENSE
 *
 * _version_ $version
 */

declare(strict_types=1);

namespace Inane\Db\Sql;

use Stringable;
use const null;

/**
 * The Builder interface declares a set of methods to assemble an SQL query.
 *
 * All of the construction steps are returning the current builder object to
 * allow chaining: $builder->select(...)->where(...)
 *
 * @version 1.0.0
 */
interface SQLQueryBuilderInterface extends Stringable {
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

    /**
     * Prepares and returns the complete SQL query string with placeholders.
     *
     * @return string The fully constructed SQL query string.
     */
    public function prepare(): string;

    /**
     * Retrieves key-value data from the query's 'where' clause.
     *
     * To be used with prepared statements and parameter binding.
     *
     * @return array The key-value data from the 'where' clause, or an empty array if it does not exist.
     */
    public function getKeyValueData(): array;
}
