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

use function array_map;
use function explode;
use function implode;

/**
 * PostgreSQL-specific SQL grammar implementation.
 *
 * Provides PostgreSQL-compatible identifier quoting, pagination fragments,
 * inserted ID retrieval clauses, and native boolean capability reporting for
 * the query builder layer.
 */
class PostgreSQLGrammar extends DatabaseGrammar {
    /**
     * Quote a PostgreSQL identifier using double quotes.
     *
     * Dot-separated identifiers are quoted part by part, so table-qualified
     * columns remain valid, while wildcard parts are preserved unquoted.
     *
     * @param string $identifier The SQL identifier, potentially containing dot-separated parts.
     *
     * @return string The quoted identifier, with wildcard parts left unchanged.
     *
     * @throws InvalidArgumentException If the provided identifier is invalid or cannot be quoted.
     */
    public function quoteIdentifier(string $identifier): string {
        // Quote each identifier segment independently to preserve table.column notation.
        $parts = explode('.', $identifier);

        return implode('.', array_map(static fn($p) => $p === '*' ? $p : "\"$p\"", $parts));
    }

    /**
     * Compile a PostgreSQL LIMIT clause with an optional OFFSET clause.
     *
     * @param int      $limit  The maximum number of rows to return.
     * @param int|null $offset The optional number of rows to skip.
     *
     * @return string The compiled PostgreSQL pagination SQL fragment.
     *
     * @throws InvalidArgumentException If the limit or offset values are invalid.
     */
    public function compileLimit(int $limit, ?int $offset): string {
        // PostgreSQL allows LIMIT on its own and OFFSET as an additional clause.
        $sql = " LIMIT $limit";
        if ($offset !== null) {
            $sql .= " OFFSET $offset";
        }

        return $sql;
    }

    /**
     * Compile the PostgreSQL clause used to return the inserted ID.
     *
     * @return string The PostgreSQL RETURNING clause for the inserted primary key.
     *
     * @throws RuntimeException If the inserted ID clause cannot be provided.
     */
    public function compileInsertGetId(): string {
        return 'RETURNING id';
    }

    /**
     * Determine whether PostgreSQL supports native boolean bindings.
     *
     * @return bool True because PostgreSQL has native boolean support.
     *
     * @throws RuntimeException If boolean support cannot be determined.
     */
    public function supportsBooleans(): bool {
        return true;
    }
}
