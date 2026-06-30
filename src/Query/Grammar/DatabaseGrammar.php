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

/**
 * Abstract class for defining database grammar.
 *
 * This class provides the foundation for database-specific grammar implementations,
 * defining methods for quoting identifiers, compiling SQL fragments, and preparing
 * bound values for the database engine.
 */
abstract class DatabaseGrammar {
    /**
     * Quote an SQL identifier for the target database engine.
     *
     * Implementations should preserve wildcard identifiers and correctly quote
     * dot-separated identifier parts such as table and column names.
     *
     * @param string $identifier The identifier to quote.
     *
     * @return string The quoted identifier.
     *
     * @throws \InvalidArgumentException If the identifier cannot be quoted.
     */
    abstract public function quoteIdentifier(string $identifier): string;

    /**
     * Compile the database-specific LIMIT/OFFSET SQL fragment.
     *
     * @param int      $limit  The maximum number of rows to return.
     * @param int|null $offset The optional number of rows to skip.
     *
     * @return string The compiled pagination SQL fragment.
     *
     * @throws \InvalidArgumentException If the limit or offset values are invalid.
     */
    abstract public function compileLimit(int $limit, ?int $offset): string;

    /**
     * Compile the database-specific expression used to retrieve an inserted ID.
     *
     * @return string The SQL expression or clause used to retrieve the inserted ID.
     *
     * @throws \RuntimeException If the database grammar cannot provide an inserted ID expression.
     */
    abstract public function compileInsertGetId(): string;

    /**
     * Determine whether the target database engine supports native booleans.
     *
     * @return bool True when native booleans are supported, otherwise false.
     *
     * @throws \RuntimeException If boolean support cannot be determined.
     */
    abstract public function supportsBooleans(): bool;

    /**
     * Normalise a bound value for the target database engine.
     *
     * Databases without native boolean support receive boolean values as integer
     *  surrogates, so generated bindings remain portable across supported drivers.
     *
     * @param mixed $value The value to prepare for database binding.
     *
     * @return mixed The original value, or an integer boolean surrogate when needed.
     *
     * @throws \RuntimeException If boolean support cannot be determined.
     */
    public function wrapValue(mixed $value): mixed {
        // Convert booleans only when the active grammar cannot bind them natively.
        if (is_bool($value) && !$this->supportsBooleans()) {
            return $value ? 1 : 0;
        }

        return $value;
    }
}
