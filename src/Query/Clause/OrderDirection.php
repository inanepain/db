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
 * Supported SQL ORDER BY directions.
 *
 * Defines the sort direction keywords understood by the query builder when
 * composing database ORDER BY expressions.
 */
enum OrderDirection: string {
    /**
     * Sort results in ascending order.
     */
    case ASC = 'ASC';

    /**
     * Sort results in descending order.
     */
    case DESC = 'DESC';
}
