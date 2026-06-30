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
 * @author   Philip Michael Raab<philip@cathedral.co.za>
 * @package  inanepain\db
 * @category db
 *
 * @license  UNLICENSE
 * @license  https://unlicense.org/UNLICENSE UNLICENSE
 *
 * _version_ $version
 */

declare(strict_types = 1);

namespace Inane\Db\Query;

/**
 * Class responsible for building SQL queries in ANSI-compliant mode.
 */
class ANSIQueryBuilder extends QueryBuilder {
    /**
     * Class constructor.
     *
     * Initialises the parent class with a specific database driver.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct(DatabaseDriver::ANSI);
    }
}
