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
 * Supported SQL query operation types.
 *
 * Defines the primary SQL statement categories understood by the query builder
 * when selecting the compiler path for generated database queries.
 */
enum QueryType: string {
    /**
     * Retrieve rows from a database table or expression.
     */
    case SELECT = 'SELECT';

    /**
     * Insert one or more rows into a database table.
     */
    case INSERT = 'INSERT';

    /**
     * Update existing rows in a database table.
     */
    case UPDATE = 'UPDATE';

    /**
     * Delete existing rows from a database table.
     */
    case DELETE = 'DELETE';
}
