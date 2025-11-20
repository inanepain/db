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

use Inane\Stdlib\Options;

use function count;
use function implode;
use function in_array;
use function is_int;
use function is_null;
use const null;

/**
 * Each Concrete Builder corresponds to a specific SQL dialect and may implement
 * the builder steps a little bit differently from the others.
 *
 * This Concrete Builder can build SQL queries compatible with ANSI SQL.
 *
 * @version 1.0.0
 */
class ANSIQueryBuilder implements SQLQueryBuilderInterface {
    /**
     * Stores the various query parts
     *
     * @var \Inane\Stdlib\Options
     */
    protected Options $query;

    public function __construct() {
        $this->reset();
    }

    protected function reset(): void {
        // $this->query = new \stdClass();
        $this->query = new Options();
    }

	protected function parseFields(array $fields): string {
		return count($fields) == 0 ? '*' : implode(', ', $fields);
//		foreach ($fields as $key => $value) {
//			if (is_array($value)) {
//				if ($key === 'count') $fields[$key] = 'COUNT(*)';
//			}
//		}
	}

	/**
	 * Build a base SELECT query.
	 *
	 * @param string $table  source table
	 * @param array  $fields the fields to show or empty for all
	 *
	 * @return SQLQueryBuilderInterface
	 */
    public function select(string $table, array $fields = []): SQLQueryBuilderInterface {
		$fieldsString = $this->parseFields($fields);

        $this->query->base = 'SELECT ' . $fieldsString . ' FROM ' . $table;
        $this->query->type = 'select';

        return $this;
    }

    /**
     * Add a WHERE condition.
     */
    public function where(string $field, string|int $value, string $operator = '='): SQLQueryBuilderInterface {
        if (!in_array($this->query->type, ['select', 'update', 'delete']))
            throw new \Exception('WHERE can only be added to SELECT, UPDATE OR DELETE');

        $quote = is_int($value) ? '' : "'";
		if (!$this->query->offsetExists('where')) $this->query->offsetSet('where', []);
        $this->query->where[] = "$field $operator $quote$value$quote";

        return $this;
    }

    /**
     * Add a LIMIT constraint.
     */
    public function limit(int $limit, ?int $offset = null): SQLQueryBuilderInterface {
        if (!in_array($this->query->type, ['select']))
            throw new \Exception('LIMIT can only be added to SELECT');

        if (!is_null($offset)) $this->query->limit = " OFFSET $offset ROWS";
        $this->query->limit .= " FETCH FIRST $limit ROWS ONLY";

        return $this;
    }

	/**
	 * Get the final query string.
	 *
	 * @return string
	 */
    public function getSQL(): string {
        $query = $this->query;
        $sql = $query->base;
        if (!empty($query->where))
            $sql .= ' WHERE ' . implode(' AND ', $query->where->toArray());

        if ($query->offsetExists('limit'))
            $sql .= $query->limit;

        return $sql . ';';
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
