<?php

/**
 * Inane: Db
 * Some helpers for database task and query construction.
 * $Id$
 * $Date$
 * PHP version 8.4
 *
 * @author   Philip Michael Raab<philip@cathedral.co.za>
 * @package  inanepain\db
 * @category db
 * @license  UNLICENSE
 * @license  https://unlicense.org/UNLICENSE UNLICENSE
 * _version_ $version
 */

declare(strict_types = 1);

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
	 * A collection of where clauses can be passed to the constructor to initialise the object.
	 * Each where clause can be passed as an array of arguments or as a WhereClause object.
	 * If an array of arrays is passed, it will be treated as a nested where clause and handled as an **OR** condition.
	 *
	 * @param array|array[]|string[] $wheres An optional array of conditions to initialise the Where object with.
	 */
	public function __construct(array $wheres = []) {
		$this->parseWheres($wheres);
	}

	/**
	 * Parses an array of where conditions and constructs valid where clauses
	 * by processing nested arrays or handling different where condition formats.
	 *
	 * @param array $wheres The array of where conditions to process. Each element can be an array,
	 *                      an instance of WhereClause, or another format representing a condition.
	 * @param bool  $or     Determines whether the conditions should be combined using OR logic.
	 *                      Defaults to false (AND logic).
	 *
	 * @return void
	 */
	private function parseWheres(array $wheres = [], bool $or = false) {
		foreach($wheres as $where) {
			if (is_array($where) && is_array($where[0])) $this->parseWheres($where, true);
			elseif (is_array($where)) {
				$add = count($where) === 2 ? (array_merge($where, ['=', $or])) : array_merge($where, [$or]);
				$this->addWhere(...$add);
			}
			elseif ($where instanceof WhereClause) $this->addWhereClause($where, $or);
		}
	}

	/**
	 * Adds a WHERE condition to the SQL query.
	 *
	 * @param string          $field    The name of the field to apply the condition on.
	 * @param string|int      $value    The value to compare the field against.
	 * @param string|Operator $operator The comparison operator to use (default is '=').
	 *
	 * @return self Returns the current instance for method chaining.
	 */
	public function addWhere(string $field, string|int $value, string|Operator $operator = '=', bool $or = false): self {
		return $this->addWhereClause(new WhereClause($field, $value, $operator), $or);
	}

	/**
	 * Adds a where clause to the list of where clauses.
	 *
	 * @param WhereClause $where The where clause to be added.
	 *
	 * @return self The current instance for method chaining.
	 */
	public function addWhereClause(WhereClause $where, bool $or = false): self {
		$join = $or ? ' or ' : ' and ';
		$this->whereClauses[] = [empty($this->whereClauses) ? '' : $join, $where];

		return $this;
	}

	/**
	 * Converts the object to its string representation.
	 *
	 * @return string The concatenated string of where clauses separated by ' and '.
	 */
	public function __toString(): string {
		$where = '';
		foreach($this->whereClauses as $clause) $where .= implode('', $clause);
		return $where;
	}
}
