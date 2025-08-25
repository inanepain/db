<?php

/**
 * Inane: Db
 *
 * Some helpers for database task and query construction.
 *
 * $Id$
 * $Date$
 *
 * PHP version 8.4
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

declare(strict_types=1);

namespace Inane\Db\Sql;

use Stringable;

use function implode;
use function is_array;

/**
 * Where
 *
 * @version 1.0.0
 */
class Where implements Stringable {
    /**
     * Where clauses
     *
     * @var \Inane\Db\Sql\WhereClause[]
     */
    protected array $whereClauses = [];

    /**
     * Constructor for the Where class.
     *
     * @param array|array[]|string[] $wheres An optional array of conditions to initialize the Where object with.
     */
    public function __construct(array $wheres = []) {
        foreach($wheres as $where)
            if (is_array($where)) $this->addWhere(...$where);
            else if ($where instanceof WhereClause) $this->addWhereClause($where);
    }

    /**
     * Adds a WHERE condition to the SQL query.
     *
     * @param string $field The name of the field to apply the condition on.
     * @param string|int $value The value to compare the field against.
     * @param string|Operator $operator The comparison operator to use (default is '=').
     *
     * @return self Returns the current instance for method chaining.
     */
    public function addWhere(string $field, string|int $value, string|Operator $operator = '='): self {
        return $this->addWhereClause(new WhereClause($field, $value, $operator));
    }

    public function addWhereClause(WhereClause $where): self {
        $this->whereClauses[] = $where;
        return $this;
    }

    public function __toString(): string {
        return "" . implode(' and ', $this->whereClauses) . "";
    }
}
