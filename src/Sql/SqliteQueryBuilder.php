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

/**
 * This Concrete Builder is compatible with SQLite.
 *
 * For the currently supported options we can inherit directly from Postgres.
 *
 * @version 1.0.0
 */
class SqliteQueryBuilder extends PostgresQueryBuilder {
}
