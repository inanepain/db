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

abstract class DatabaseGrammar {
    abstract public function quoteIdentifier(string $identifier): string;
    abstract public function compileLimit(int $limit, ?int $offset): string;
    abstract public function compileInsertGetId(): string;
    abstract public function supportsBooleans(): bool;

    public function wrapValue(mixed $value): mixed {
        if (is_bool($value) && !$this->supportsBooleans()) {
            return $value ? 1 : 0;
        }
        return $value;
    }
}
