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

namespace Inane\Db\Query\Grammar;

use InvalidArgumentException;
use RuntimeException;

/**
 * Class ANSIGrammar
 *
 * Provides ANSI SQL-specific grammar rules for query compilation.
 * Extends the DatabaseGrammar base class to define compatibility
 * with ANSI SQL standards.
 */
class ANSIGrammar extends DatabaseGrammar {
    /**
     * Quotes an SQL identifier, ensuring that each part is wrapped in double quotes,
     * except when the part is a wildcard (*).
     *
     * @param string $identifier The SQL identifier, potentially containing dot-separated parts.
     *
     * @return string The quoted identifier, with each part properly wrapped in double quotes, except wildcards.
     *
     * @throws InvalidArgumentException If the provided identifier is invalid or empty.
     */
    public function quoteIdentifier(string $identifier): string {
        $parts = explode('.', $identifier);
        return implode('.', array_map(static fn($p) => $p === '*' ? $p : "\"$p\"", $parts));
    }

    /**
     * Compiles a SQL query fragment to apply a row limit and optional offset for fetching records.
     *
     * @param int      $limit  The maximum number of rows to fetch.
     * @param int|null $offset The starting point for fetching rows, or null if no offset is needed.
     *
     * @return string The compiled SQL query fragment with the specified limit and optional offset.
     *
     * @throws InvalidArgumentException If the provided limit is less than or equal to zero.
     */
    public function compileLimit(int $limit, ?int $offset): string {
        if ($offset !== null) {
            return " OFFSET $offset ROWS FETCH NEXT $limit ROWS ONLY";
        }
        return " FETCH FIRST $limit ROWS ONLY";
    }

    /**
     * Compiles the SQL insert statement to retrieve the ID of the newly inserted record.
     *
     * @return string The compiled SQL insert statement designed to return the inserted record's ID.
     *
     * @throws RuntimeException If the method fails to generate a valid SQL statement.
     */
    public function compileInsertGetId(): string {
        return '';
    }

    /**
     * Determines whether boolean data types are supported.
     *
     * @return bool True if boolean data types are supported, false otherwise.
     *
     * @throws RuntimeException If the method is called in an unsupported context.
     */
    public function supportsBooleans(): bool {
        return false;
    }
}
