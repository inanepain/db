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
 * Immutable value object describing a SQL JOIN clause.
 *
 * Stores the join type, target table, and comparison parts required to compile
 * a database join expression such as `INNER JOIN users ON posts.user_id = users.id`.
 */
readonly class JoinClause {
    /**
     * Create a join clause definition.
     *
     * @param JoinType $type     The SQL join type to apply.
     * @param string   $table    The table being joined to the query.
     * @param string   $first    The left-hand column or expression for the join comparison.
     * @param string   $operator The comparison operator used between both join expressions.
     * @param string   $second   The right-hand column or expression for the join comparison.
     *
     * @return void
     *
     * @throws \InvalidArgumentException If any join clause value is invalid.
     */
    public function __construct(
        public JoinType $type,
        public string $table,
        public string $first,
        public string $operator,
        public string $second
    ) {
        // Constructor property promotion assigns all immutable join metadata.
    }
}
