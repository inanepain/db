<?php

/**
 * Inane: Db
 *
 * Inane Database
 *
 * PHP version 8.1
 *
 * @author Philip Michael Raab<peep@inane.co.za>
 * @package Inane\Db
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
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

    public function __construct(array $where = []) {
        foreach($where as $w)
            if (is_array($w)) $this->addWhere(...$w);
            else if ($w instanceof WhereClause) $this->addWhereClause($w);
    }

    public function addWhere(string $field, string|int $value, string $operator = '='): self {
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
