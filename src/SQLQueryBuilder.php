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

class SQLQueryBuilder implements SQLQueryBuilderInterface {
    protected ArrayObject $queryProperties;

    public function __construct() {
        $this->reset();
    }

    protected function reset(): void {
        $this->queryProperties = new ArrayObject(['where' => []], ArrayObject::ARRAY_AS_PROPS);
    }

    public function select(string $table, array $fields): SQLQueryBuilderInterface {
        $this->queryProperties->select = [
            'table' => $table,
            'fields' => $fields
        ];

        return $this;
    }

    public function where(string $field, string|int $value, string $operator = '='): SQLQueryBuilderInterface {
        $this->queryProperties->where[] = [
            'field' => $field,
            'value' => $value,
            'operator' => $operator,
        ];

        return $this;
    }

    public function limit(int $start, ?int $offset = null): SQLQueryBuilderInterface {
        $this->queryProperties->limit = [
            'start' => $start,
            'offset' => $offset,
        ];

        return $this;
    }

    public function getSQL(): string {
        return serialize($this->queryProperties);
    }

    public function getSQLFor(SQLQueryBuilderInterface $QueryBuilder): string {
        $QueryBuilder->select(...$this->queryProperties->select);

        foreach ($this->queryProperties->where as $where) {
            // \var_dump($where); die();
            $QueryBuilder->where(...$where);
        }

        $QueryBuilder->limit(...$this->queryProperties->limit);

        return $QueryBuilder->getSQL();
    }
}
