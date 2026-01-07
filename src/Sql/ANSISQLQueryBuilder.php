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

use Inane\Stdlib\Exception\Exception;
use Inane\Stdlib\Options;
use function count;
use function implode;
use function in_array;
use function is_null;
use const null;

/**
 * Each Concrete Builder corresponds to a specific SQL dialect and may implement
 * the builder steps a little bit differently from the others.
 *
 * This Concrete Builder can build SQL queries compatible with ANSI SQL.
 *
 * **This is also used as a good base for most of the other Builders.**
 *
 * @version 1.0.0
 */
class ANSISQLQueryBuilder implements SQLQueryBuilderInterface {
	/**
	 * Stores the various query parts
	 *
	 * @var Options
	 */
	protected Options $query;

	#region Construction
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
		$this->query = new Options();
	}
	#endregion Construction

	#region Query Methods
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
	 * @throws Exception If the query type is not SELECT, UPDATE, or DELETE.
	 */
	public function where(string $field, string|int $value, string $operator = '='): SQLQueryBuilderInterface {
		if (!in_array($this->query->type, ['select', 'update', 'delete']))
			throw new Exception('WHERE can only be added to SELECT, UPDATE OR DELETE');

		if (!$this->query->offsetExists('where')) $this->query->offsetSet('where', new Where());
		$this->query->where->addWhere($field, $value, $operator);

		return $this;
	}

	/**
	 * Adds one or more conditions to the "WHERE" clause of the SQL query.
	 *
	 * @param array|Where $whereGrp The conditions to be added. Can be an instance of Clause
	 *                             or an array of conditions to initialize a new Clause object.
	 *
	 * @return SQLQueryBuilderInterface The current instance of the query builder for method chaining.
	 */
	public function whereReplace(array|Where $whereGrp): SQLQueryBuilderInterface {
		$where = $whereGrp instanceof Where ? $whereGrp : new Where($whereGrp);
		$this->query->offsetSet('where', $where);
		return $this;
	}

    /**
     * Add a LIMIT constraint.
     *
     * @throws Exception
     */
	public function limit(int $limit, ?int $offset = null): SQLQueryBuilderInterface {
		if ($this->query->type !== 'select')
			throw new Exception('LIMIT can only be added to SELECT');

		if (!is_null($offset)) $this->query->limit = " OFFSET $offset ROWS";
		$this->query->limit .= " FETCH FIRST $limit ROWS ONLY";

		return $this;
	}
	#endregion Query Methods

	#region Output
    /**
     * Prepares and returns the complete SQL query string with placeholders.
     *
     * @return string The fully constructed SQL query string.
     */
    public function prepare(): string {
        $query = $this->query;
        $sql = $query->base;
        if ($query->offsetExists('where'))
            $sql .= ' WHERE ' . $query->where->prepare();

        if ($query->offsetExists('limit'))
            $sql .= $query->limit;

        return $sql . ';';
    }

    /**
     * Retrieves key-value data from the query's 'where' clause.
     *
     * To be used with prepared statements and parameter binding.
     *
     * @return array The key-value data from the 'where' clause, or an empty array if it does not exist.
     */
    public function getKeyValueData(): array {
        return $this?->query?->where?->getData() ?? [];
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
	#endregion Output

	#region Utility
	/**
	 * Parses an array of fields and generates a string for use in a SQL query.
	 * If the array is empty, it defaults to returning "*".
	 *
	 * @param array $fields The array of fields to be parsed.
	 *
	 * @return string The resulting string of parsed fields, separated by commas, or "*" if no fields are provided.
	 */
	protected function parseFields(array $fields): string {
		return count($fields) === 0 ? '*' : implode(', ', $fields);
	}
	#endregion Utility
}
