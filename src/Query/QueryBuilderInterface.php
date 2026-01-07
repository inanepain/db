<?php

/**
 * Inane: Db
 *
 * Some helpers for database task and query construction.
 *
 * $Id$
 * $Date$
 *
 * PHP version 8.5
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

declare(strict_types = 1);

namespace Inane\Db\Query;

use Inane\Db\Query\Clause\OrderDirection;
use Inane\Db\Query\Grammar\DatabaseGrammar;

interface QueryBuilderInterface {
    /**
     * Set the table which the query is targeting.
     *
     * @param string $table
     *
     * @return self
     */
    public function table(string $table): self;

    /**
     * Set the columns to be selected.
     *
     * @param string|array ...$columns
     *
     * @return self
     */
    public function select(string|array ...$columns): self;

    /**
     * Set the query type to INSERT and set the data.
     *
     * @param array $data
     *
     * @return self
     */
    public function insert(array $data): self;

    /**
     * Set the query type to UPDATE and set the data.
     *
     * @param array $data
     *
     * @return self
     */
    public function update(array $data): self;

    /**
     * Set the query type to DELETE.
     *
     * @return self
     */
    public function delete(): self;

    /**
     * Add a basic where clause to the query.
     *
     * @param string $column
     * @param mixed $operator
     * @param mixed $value
     *
     * @return self
     */
    public function where(string $column, mixed $operator = null, mixed $value = null): self;

    /**
     * Add multiple where clauses to the query.
     *
     * @param array $conditions
     *
     * @return self
     */
    public function wheres(array $conditions): self;

    /**
     * Add an OR where clause to the query.
     *
     * @param string $column
     * @param mixed $operator
     * @param mixed $value
     *
     * @return self
     */
    public function orWhere(string $column, mixed $operator = null, mixed $value = null): self;

    /**
     * Add a WHERE IN clause to the query.
     *
     * @param string $column
     * @param array $values
     *
     * @return self
     */
    public function whereIn(string $column, array $values): self;

    /**
     * Add a WHERE IS NULL clause to the query.
     *
     * @param string $column
     *
     * @return self
     */
    public function whereNull(string $column): self;

    /**
     * Add a WHERE IS NOT NULL clause to the query.
     *
     * @param string $column
     *
     * @return self
     */
    public function whereNotNull(string $column): self;

    /**
     * Add a WHERE LIKE clause to the query.
     *
     * @param string $column
     * @param string $value
     *
     * @return self
     */
    public function whereLike(string $column, string $value): self;

    /**
     * Add a WHERE BETWEEN clause to the query.
     *
     * @param string $column
     * @param array $values
     *
     * @return self
     */
    public function whereBetween(string $column, array $values): self;

    /**
     * Add a JOIN clause to the query.
     *
     * @param string $table
     * @param string $first
     * @param string|null $operator
     * @param string|null $second
     *
     * @return self
     */
    public function join(string $table, string $first, ?string $operator = null, ?string $second = null): self;

    /**
     * Add a LEFT JOIN clause to the query.
     *
     * @param string $table
     * @param string $first
     * @param string|null $operator
     * @param string|null $second
     *
     * @return self
     */
    public function leftJoin(string $table, string $first, ?string $operator = null, ?string $second = null): self;

    /**
     * Add a RIGHT JOIN clause to the query.
     *
     * @param string $table
     * @param string $first
     * @param string|null $operator
     * @param string|null $second
     *
     * @return self
     */
    public function rightJoin(string $table, string $first, ?string $operator = null, ?string $second = null): self;

    /**
     * Add an ORDER BY clause to the query.
     *
     * @param string $column
     * @param OrderDirection|string $direction
     *
     * @return self
     */
    public function orderBy(string $column, OrderDirection|string $direction = OrderDirection::ASC): self;

    /**
     * Add a GROUP BY clause to the query.
     *
     * @param string|array ...$columns
     *
     * @return self
     */
    public function groupBy(string|array ...$columns): self;

    /**
     * Add a HAVING clause to the query.
     *
     * @param string $column
     * @param string $operator
     * @param mixed $value
     *
     * @return self
     */
    public function having(string $column, string $operator, mixed $value): self;

    /**
     * Set the limit for the query.
     *
     * @param int $limit
     *
     * @return self
     */
    public function limit(int $limit): self;

    /**
     * Set the offset for the query.
     *
     * @param int $offset
     *
     * @return self
     */
    public function offset(int $offset): self;

    /**
     * Convert the query to its SQL representation.
     *
     * @return string
     */
    public function toSql(): string;

    /**
     * Get the query bindings.
     *
     * @return array
     */
    public function getBindings(): array;

    /**
     * Set the database driver and initialize the grammar.
     *
     * @param DatabaseDriver $driver
     *
     * @return self
     */
    public function setDriver(DatabaseDriver $driver): self;

    /**
     * Get the database grammar.
     *
     * @return DatabaseGrammar
     */
    public function getGrammar(): DatabaseGrammar;
}
