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

enum DatabaseDriver: string {
    case MYSQL = 'mysql';
    case POSTGRESQL = 'pgsql';
    case SQLITE = 'sqlite';
    case ANSI = 'ansi';
}
