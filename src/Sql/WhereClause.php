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

/**
 * WhereClause
 *
 * @version 1.0.0
 */
class WhereClause implements Stringable {
    /**
     * WhereClause constructor
     *
     * @param string $field field
     * @param string|int $value value
     * @param string $operator operator
     *
     * @return void
     */
    public function __construct(
        protected string $field,
        protected string|int $value,
        protected string|Operator $operator = '=',
    ) {
    }

    public function __invoke(): string {
        return "{$this}";
    }

    /**
     * Create WhereClause from array
     *
     * @param array $where where properties
     *
     * @return static where clause
     */
    public static function fromArray(array $where): static {
        return new static(...$where);
    }

    /**
     * SQL where as string
     *
     * @return string where clause
     */
    public function __toString(): string {
        $quote = is_int($this->value) ? '' : "'";
        return "$this->field $this->operator $quote$this->value$quote";
    }

    /**
     * Export as array
     *
     * @return array where properties
     */
    public function export(): array {
        return [
            'field' => $this->field,
            'value' => $this->value,
            'operator' => $this->operator,
        ];
    }

    /**
     * Get Field
     *
     * @return string field
     */
    public function getField(): string {
        return $this->field;
    }
    /**
     * Set Field
     *
     * @param string $field field
     *
     * @return \Inane\Db\Sql\WhereClause
     */
    public function setField(string $field): self {
        $this->field = $field;
        return $this;
    }

    /**
     * Get Value
     *
     * @return string|int value
     */
    public function getValue(): string|int {
        return $this->value;
    }
    /**
     * Set Value
     *
     * @param string|int $value value
     *
     * @return \Inane\Db\Sql\WhereClause
     */
    public function setValue(string|int $value): self {
        $this->value = $value;
        return $this;
    }

    /**
     * Get Operator
     *
     * @return string|Operator Operator
     */
    public function getOperator(): string|Operator {
        return $this->operator;
    }
    /**
     * Set Operator
     *
     * @param string|Operator $operator operator
     *
     * @return \Inane\Db\Sql\WhereClause
     */
    public function setOperator(string|Operator $operator = '='): self {
        $this->operator = $operator;
        return $this;
    }
}
