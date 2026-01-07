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

/**
 * This Concrete Builder is compatible with SQLite.
 *
 * For the currently supported options we can inherit directly from Postgres.
 *
 * @version 1.0.0
 */
class SqliteSQLQueryBuilder extends PostgresQueryBuilder {
}
