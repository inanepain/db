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

namespace Inane\Db\Query\Clause;

/**
 * Represents a SQL WHERE clause for use in query building.
 *
 * This class provides a structured way to define a filtering condition
 * for database queries. It supports various types of clauses such as
 * basic comparisons, IN, NULL, LIKE, and BETWEEN.
 *
 * @param string     $type     The type of WHERE clause, such as basic, in, null, like, or between.
 * @param string     $column   The column or expression being filtered in the clause.
 * @param string     $operator The comparison operator for the clause (e.g., '=', '!=', '<', etc.).
 * @param mixed      $value    The comparison value for single-value clauses.
 * @param string     $boolean  The boolean connector (e.g., AND, OR) used to join this clause with others.
 * @param array|null $values   The list of values for multi-value clauses like IN or BETWEEN.
 *
 * @return void
 *
 * @throws \InvalidArgumentException If the provided values for the clause are invalid.
 */
readonly class WhereClause {
    /**
     * Create a where clause definition.
     *
     * @param string     $type     The where clause type, such as basic, in, null, like, or between.
     * @param string     $column   The column or expression being filtered.
     * @param string     $operator The comparison operator used by the clause.
     * @param mixed      $value    The comparison value for single-value clauses.
     * @param string     $boolean  The boolean connector used to join this clause to previous clauses.
     * @param array|null $values   The value list for multi-value clauses, such as IN or BETWEEN.
     *
     * @return void
     *
     * @throws \InvalidArgumentException If any where clause value is invalid.
     */
    public function __construct(
        public string $type,
        public string $column,
        public string $operator = '=',
        public mixed $value = null,
        public string $boolean = 'AND',
        public ?array $values = null
    ) {
    }
}
