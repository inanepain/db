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

readonly class JoinClause {
    /**
     * JoinClause constructor.
     *
     * @param JoinType $type
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     */
    public function __construct(
        public JoinType $type,
        public string $table,
        public string $first,
        public string $operator,
        public string $second
    ) {
    }
}
