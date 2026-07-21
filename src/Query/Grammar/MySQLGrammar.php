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
 * MySQL-specific SQL grammar implementation.
 *
 * Provides MySQL-compatible identifier quoting, pagination fragments, inserted
 * ID retrieval expressions, and native boolean capability reporting for the
 * query builder layer.
 */
class MySQLGrammar extends DatabaseGrammar {
    /**
     * Quote a MySQL identifier using backticks.
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
        $parts = explode('.', $identifier);
        return implode('.', array_map(static fn($p) => $p === '*' ? $p : "`$p`", $parts));
    }

    /**
     * Compile a MySQL LIMIT clause with an optional OFFSET clause.
     *
     * @param int      $limit  The maximum number of rows to return.
     * @param int|null $offset The optional number of rows to skip.
     *
     * @return string The compiled MySQL pagination SQL fragment.
     *
     * @throws InvalidArgumentException If the limit or offset values are invalid.
     */
    public function compileLimit(int $limit, ?int $offset): string {
        // MySQL accepts LIMIT on its own and OFFSET only when a limit is present.
        $sql = " LIMIT $limit";
        if ($offset !== null) {
            $sql .= " OFFSET $offset";
        }
        return $sql;
    }

    /**
     * Compile the MySQL expression used to retrieve the last inserted ID.
     *
     * @return string The MySQL function call returning the connection's last inserted ID.
     *
     * @throws RuntimeException If the inserted ID expression cannot be provided.
     */
    public function compileInsertGetId(): string {
        return 'LAST_INSERT_ID()';
    }

    /**
     * Determine whether MySQL supports native boolean bindings.
     *
     * @return bool True because this grammar treats MySQL booleans as natively supported.
     *
     * @throws RuntimeException If boolean support cannot be determined.
     */
    public function supportsBooleans(): bool {
        return true;
    }
}
