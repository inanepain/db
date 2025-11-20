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

	/**
	 * Initialises the object and sets up its initial state.
	 *
	 * @return void
	 */
	public function __construct() {
        $this->reset();
    }

	/**
	 * Resets the query property to a new instance of the Options class.
	 *
	 * @return void
	 */
	protected function reset(): void {
        // $this->query = new \stdClass();
        $this->query = new Options();
    }

	/**
	 * Parses an array of fields and generates a string for use in a SQL query.
	 * If the array is empty, it defaults to returning "*".
	 *
	 * @param array $fields The array of fields to be parsed.
	 *
	 * @return string The resulting string of parsed fields, separated by commas, or "*" if no fields are provided.
	 */
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
	 * Adds a WHERE condition to the SQL query. This method supports SELECT, UPDATE, or DELETE queries only.
	 *
	 * @param string     $field    The name of the database column to compare.
	 * @param string|int $value    The value to compare against the specified column.
	 * @param string     $operator The comparison operator to use (e.g., '=', '>', '<'). Default is '='.
	 *
	 * @return SQLQueryBuilderInterface Returns the current instance of the query builder for method chaining.
	 *
	 * @throws \Exception If the query type is not SELECT, UPDATE, or DELETE.
	 */
    public function where(string $field, string|int $value, string $operator = '='): SQLQueryBuilderInterface {
        if (!in_array($this->query->type, ['select', 'update', 'delete']))
            throw new \Exception('WHERE can only be added to SELECT, UPDATE OR DELETE');

//        $quote = is_int($value) ? '' : "'";
		if (!$this->query->offsetExists('where')) $this->query->offsetSet('where', new Where());
	    $this->query->where->addWhere($field, $value, $operator);
//        $this->query->where[] = "$field $operator $quote$value$quote";

        return $this;
    }

	/**
	 * Adds one or more conditions to the "WHERE" clause of the SQL query.
	 *
	 * @param array|Where $wheres  The conditions to be added. Can be an instance of Where
	 *                             or an array of conditions to initialize a new Where object.
	 *
	 * @return SQLQueryBuilderInterface The current instance of the query builder for method chaining.
	 */
	public function wheres(array|Where $wheres): SQLQueryBuilderInterface {
		$where = $wheres instanceof Where ? $wheres : new Where($wheres);
		$this->query->offsetSet('where', $where);
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
		if ($query->offsetExists('where'))
			$sql .= ' WHERE ' . (string)$query->where;

//        if (!empty($query->where))
//            $sql .= ' WHERE ' . implode(' AND ', $query->where->toArray());

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
