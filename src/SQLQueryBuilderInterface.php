<?php

/**
 * Inane: Db
 *
 * Inane Database
 *
 * PHP version 8.1
 *
 * @author Philip Michael Raab<peep@inane.co.za>
 * @package Inane\Stdlib
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 */

declare(strict_types=1);

namespace Inane\Db;

use ArrayObject;

use function implode;
use function in_array;
use function is_int;
use function is_null;
use function serialize;
use const null;

/**
 * The Builder interface declares a set of methods to assemble an SQL query.
 *
 * All of the construction steps are returning the current builder object to
 * allow chaining: $builder->select(...)->where(...)
 */
interface SQLQueryBuilderInterface {
    public function select(string $table, array $fields): SQLQueryBuilderInterface;

    public function where(string $field, string|int $value, string $operator = '='): SQLQueryBuilderInterface;

    public function limit(int $start, ?int $offset = null): SQLQueryBuilderInterface;

    // +100 other SQL syntax methods...

    public function getSQL(): string;
}
