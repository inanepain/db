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

namespace Inane\Db\Query\Clause;

readonly class WhereClause {
    /**
     * WhereClause constructor.
     *
     * @param string $type
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @param string $boolean
     * @param array|null $values
     */
    public function __construct(
        public string $type,
        public string $column,
        public string $operator = '=',
        public mixed $value = null,
        public string $boolean = 'AND',
        public ?array $values = null
    ) {
    }
}
