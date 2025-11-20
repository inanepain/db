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

use Inane\Stdlib\Exception\RuntimeException;
use Inane\Stdlib\Options;

use function serialize;
use function unserialize;
use const null;

/**
 * SQLQueryBuilder
 *
 * @version 1.0.0
 */
class SQLQueryBuilder implements SQLQueryBuilderInterface {
    /**
     * Stores the various query parts
     *
     * @var \Inane\Stdlib\Options
     */
    protected Options $queryProperties;

    /**
     * SQLQueryBuilder constructor
     *
     * @param string|null $serialised
     */
    public function __construct(?string $serialised = null) {
        $this->reset($serialised);
    }

    /**
     * Start a new query or restore a serialised one.
     *
     * @param string|null $serialised
     *
     * @return void
     */
    protected function reset(?string $serialised = null): void {
        $this->queryProperties = $serialised ? unserialize($serialised, Options::class) : new Options();
    }

    /**
     * Create a select query
     *
     * @param string $table
     * @param array $fields
     *
     * @return \Inane\Db\SQLQueryBuilderInterface
     */
    public function select(string $table, array $fields = []): SQLQueryBuilderInterface {
        $this->queryProperties->select = [
            'table' => $table,
            'fields' => $fields
        ];
        $this->queryProperties->type = 'select';
        $this->queryProperties->table = 'table';
        $this->queryProperties->fields = $fields;

        return $this;
    }

	/**
	 * Add a where clause
	 *
	 * @param string          $field
	 * @param string|int      $value
	 * @param string|Operator $operator
	 *
	 * @return SQLQueryBuilderInterface
	 *
	 * @throws RuntimeException
	 */
    public function where(string $field, string|int $value, string|Operator $operator = '='): SQLQueryBuilderInterface {
	    if (!$this->queryProperties->offsetExists('where')) $this->queryProperties->offsetSet('where', []);
        $this->queryProperties->where[] = [
            'field' => $field,
            'value' => $value,
            'operator' => $operator,
        ];

        return $this;
    }

    /**
     * Limit query response
     *
     * @param int $limit
     * @param null|int $offset
     *
     * @return \Inane\Db\SQLQueryBuilderInterface
     */
    public function limit(int $limit, ?int $offset = null): SQLQueryBuilderInterface {
        $this->queryProperties->limit = [
            'limit' => $limit,
            'offset' => $offset,
        ];

        return $this;
    }

    /**
     * Get the serialised query
     *
     * @return string
     */
    public function getSQL(): string {
        return serialize($this->queryProperties);
    }

    /**
     * Get the query as string for specified database type
     *
     * @param \Inane\Db\SQLQueryBuilderInterface $QueryBuilder
     *
     * @return string
     */
    public function getSQLFor(SQLQueryBuilderInterface $QueryBuilder): string {
        $QueryBuilder->select(...$this->queryProperties->select->toArray());

        foreach ($this->queryProperties->where ?: [] as $where)
            $QueryBuilder->where(...$where);

        $QueryBuilder->limit(...$this->queryProperties->limit->toArray());

        return $QueryBuilder->getSQL();
    }

	/**
	 * Converts the object to its string representation.
	 *
	 * @return string The SQL string representation of the object.
	 */
	public function __toString(): string {
		return $this->getSQL();
	}
}
