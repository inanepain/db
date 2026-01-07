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

class SQLiteGrammar extends DatabaseGrammar {
    public function quoteIdentifier(string $identifier): string {
        $parts = explode('.', $identifier);
        return implode('.', array_map(fn($p) => $p === '*' ? $p : "\"$p\"", $parts));
    }

    public function compileLimit(int $limit, ?int $offset): string {
        $sql = " LIMIT $limit";
        if ($offset !== null) {
            $sql .= " OFFSET $offset";
        }
        return $sql;
    }

    public function compileInsertGetId(): string {
        return 'last_insert_rowid()';
    }

    public function supportsBooleans(): bool {
        return false;
    }
}
