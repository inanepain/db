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

/**
 * Represents the supported database driver types for database connections.
 */
enum DatabaseDriver: string {
    /**
     * MySQL-compatible database driver.
     *
     * Used for MySQL and compatible engines that share the same SQL dialect.
     */
    case MYSQL = 'mysql';

    /**
     * PostgreSQL database driver.
     *
     * Used when query generation must account for PostgreSQL-specific syntax.
     */
    case POSTGRESQL = 'pgsql';

    /**
     * SQLite database driver.
     *
     * Used for file-backed or in-memory SQLite database connections.
     */
    case SQLITE = 'sqlite';

    /**
     * ANSI SQL driver.
     *
     * Used as the neutral fallback for standard SQL generation.
     */
    case ANSI = 'ansi';
}
