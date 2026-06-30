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

use function array_map;
use function explode;
use function implode;

/**
 * SQLite-specific SQL grammar implementation.
 *
 * Provides SQLite-compatible identifier quoting, pagination fragments, inserted
 * ID retrieval expressions, and native boolean capability reporting for the
 * query builder layer.
 */
class SQLiteGrammar extends DatabaseGrammar {
    /**
     * Quote an SQLite identifier using double quotes.
     *
     * Dot-separated identifiers are quoted part by part, so table-qualified
     * columns remain valid, while wildcard parts are preserved unquoted.
     *
     * @param string $identifier The SQL identifier, potentially containing dot-separated parts.
     *
     * @return string The quoted identifier, with wildcard parts left unchanged.
     *
     * @throws \InvalidArgumentException If the provided identifier is invalid or cannot be quoted.
     */
    public function quoteIdentifier(string $identifier): string {
        // Quote each identifier segment independently to preserve table.column notation.
        $parts = explode('.', $identifier);

        return implode('.', array_map(static fn($p) => $p === '*' ? $p : "\"$p\"", $parts));
    }

    /**
     * Compile an SQLite LIMIT clause with an optional OFFSET clause.
     *
     * @param int      $limit  The maximum number of rows to return.
     * @param int|null $offset The optional number of rows to skip.
     *
     * @return string The compiled SQLite pagination SQL fragment.
     *
     * @throws \InvalidArgumentException If the limit or offset values are invalid.
     */
    public function compileLimit(int $limit, ?int $offset): string {
        // SQLite accepts LIMIT on its own and OFFSET only when a limit is present.
        $sql = " LIMIT $limit";
        if ($offset !== null) {
            $sql .= " OFFSET $offset";
        }

        return $sql;
    }

    /**
     * Compile the SQLite expression used to retrieve the last inserted row ID.
     *
     * @return string The SQLite function call returning the connection's last inserted row ID.
     *
     * @throws \RuntimeException If the inserted ID expression cannot be provided.
     */
    public function compileInsertGetId(): string {
        return 'last_insert_rowid()';
    }

    /**
     * Determine whether SQLite supports native boolean bindings.
     *
     * @return bool False because SQLite stores booleans as integer surrogate values.
     *
     * @throws \RuntimeException If boolean support cannot be determined.
     */
    public function supportsBooleans(): bool {
        return false;
    }
}
