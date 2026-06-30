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
 * Supported SQL JOIN clause types.
 *
 * Defines the join keywords understood by the query builder when composing
 * database JOIN expressions.
 */
enum JoinType: string {
    /**
     * Match rows where both joined tables satisfy the join condition.
     */
    case INNER = 'INNER';

    /**
     * Return all rows from the left table and matching rows from the joined table.
     */
    case LEFT = 'LEFT';

    /**
     * Return all rows from the joined table and matching rows from the left table.
     */
    case RIGHT = 'RIGHT';

    /**
     * Return the Cartesian product of both joined tables.
     */
    case CROSS = 'CROSS';
}
