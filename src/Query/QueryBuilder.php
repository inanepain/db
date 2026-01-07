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
 * @author   Philip Michael Raab<philip@cathedral.co.za>
 * @package  inanepain\db
 * @category db
 *
 * @license  UNLICENSE
 * @license  https://unlicense.org/UNLICENSE UNLICENSE
 *
 * _version_ $version
 */

declare(strict_types = 1);

namespace Inane\Db\Query;

use Inane\Db\Query\Clause\{
    JoinClause,
    JoinType,
    OrderDirection,
    QueryType,
    WhereClause};
use Inane\Db\Query\Grammar\{
    ANSIGrammar,
    DatabaseGrammar,
    MySQLGrammar,
    PostgreSQLGrammar,
    SQLiteGrammar};
use function array_fill;
use function array_keys;
use function array_map;
use function array_reduce;
use function array_values;
use function count;
use function func_num_args;
use function implode;
use function in_array;
use function is_array;
use function strtoupper;

class QueryBuilder implements QueryBuilderInterface {
    //#region Properties
    /** @var string|null The table which the query is targeting. */
    private ?string $table = null;

    /** @var QueryType|null The query type. */
    private ?QueryType $type = null;

    /** @var array The columns to be selected. */
    private array $columns = ['*'];

    /** @var WhereClause[] The WHERE clauses. */
    private array $wheres = [];

    /** @var JoinClause[] The JOIN clauses. */
    private array $joins = [];

    /** @var array The ORDER BY clauses. */
    private array $orderBy = [];

    /** @var array The GROUP BY clauses. */
    private array $groupBy = [];

    /** @var array The HAVING clauses. */
    private array $having = [];

    /** @var int|null The limit for the query. */
    private ?int $limit = null;

    /** @var int|null The offset for the query. */
    private ?int $offset = null;

    /** @var array The data for INSERT or UPDATE. */
    private array $data = [];

    /** @var array The query bindings. */
    private array $bindings = [];

    /** @var DatabaseGrammar The database grammar. */
    private DatabaseGrammar $grammar;

    //#endregion Properties

    /**
     * QueryBuilder constructor.
     *
     * @param DatabaseDriver $driver
     */
    public function __construct(DatabaseDriver $driver = DatabaseDriver::MYSQL) {
        $this->setDriver($driver);
    }

    #region Build Statement Methods
    /**
     * Build the SELECT query string.
     *
     * @return string
     */
    private function buildSelect(): string {
        $quotedColumns = array_map(fn($col) => $this->grammar->quoteIdentifier($col), $this->columns);

        $sql = 'SELECT ' . implode(', ', $quotedColumns);
        $sql .= ' FROM ' . $this->grammar->quoteIdentifier($this->table);
        $sql .= $this->buildJoins();
        $sql .= $this->buildWheres();
        $sql .= $this->buildGroupBy();
        $sql .= $this->buildHaving();
        $sql .= $this->buildOrderBy();
        $sql .= $this->buildLimit();

        return $sql;
    }

    /**
     * Build the INSERT query string.
     *
     * @return string
     */
    private function buildInsert(): string {
        $columns = array_keys($this->data);
        $quotedColumns = array_map(fn($col) => $this->grammar->quoteIdentifier($col), $columns);

        $sql = 'INSERT INTO ' . $this->grammar->quoteIdentifier($this->table);
        $sql .= ' (' . implode(', ', $quotedColumns) . ')';
        $sql .= ' VALUES (';

        $placeholders = array_map(fn() => '?', $this->data);
        $this->bindings = array_map(fn($val) => $this->grammar->wrapValue($val), array_values($this->data));

        $sql .= implode(', ', $placeholders) . ')';

        return $sql;
    }

    /**
     * Build the UPDATE query string.
     *
     * @return string
     */
    private function buildUpdate(): string {
        $sql = 'UPDATE ' . $this->grammar->quoteIdentifier($this->table) . ' SET ';

        $sets = array_map(function($column) {
            $this->bindings[] = $this->grammar->wrapValue($this->data[$column]);

            return $this->grammar->quoteIdentifier($column) . ' = ?';
        }, array_keys($this->data));

        $sql .= implode(', ', $sets);
        $sql .= $this->buildWheres();

        return $sql;
    }

    /**
     * Build the DELETE query string.
     *
     * @return string
     */
    private function buildDelete(): string {
        return 'DELETE FROM ' . $this->grammar->quoteIdentifier($this->table) . $this->buildWheres();
    }
    #endregion Build Statement Methods

    #region Build Statement Properties
    /**
     * Build the JOIN clauses.
     *
     * @return string
     */
    private function buildJoins(): string {
        if (empty($this->joins)) {
            return '';
        }

        return array_reduce($this->joins, function($sql, JoinClause $join) {
            $table = $this->grammar->quoteIdentifier($join->table);
            $first = $this->grammar->quoteIdentifier($join->first);
            $second = $this->grammar->quoteIdentifier($join->second);

            return $sql . ' ' . $join->type->value . ' JOIN ' . $table . ' ON ' . $first . ' ' . $join->operator . ' ' . $second;
        }, '');
    }

    /**
     * Build the WHERE clauses.
     *
     * @return string
     */
    private function buildWheres(): string {
        if (empty($this->wheres)) {
            return '';
        }

        $sql = ' WHERE ';
        $conditions = [];

        foreach($this->wheres as $i => $where) {
            $boolean = $i === 0 ? '' : ' ' . $where->boolean . ' ';
            $quotedColumn = $this->grammar->quoteIdentifier($where->column);

            $condition = match ($where->type) {
                'basic' => function() use ($where, $quotedColumn) {
                    $this->bindings[] = $this->grammar->wrapValue($where->value);

                    return $quotedColumn . ' ' . $where->operator . ' ?';
                },
                'in' => function() use ($where, $quotedColumn) {
                    $placeholders = array_fill(0, count($where->values), '?');
                    $wrappedValues = array_map(fn($val) => $this->grammar->wrapValue($val), $where->values);
                    $this->bindings = [...$this->bindings, ...$wrappedValues];

                    return $quotedColumn . ' IN (' . implode(', ', $placeholders) . ')';
                },
                'between' => function() use ($where, $quotedColumn) {
                    $this->bindings[] = $this->grammar->wrapValue($where->values[0]);
                    $this->bindings[] = $this->grammar->wrapValue($where->values[1]);

                    return $quotedColumn . ' BETWEEN ? AND ?';
                },
                'null' => fn() => $quotedColumn . ' IS NULL',
                'not_null' => fn() => $quotedColumn . ' IS NOT NULL',
            };

            $conditions[] = $boolean . $condition();
        }

        return $sql . implode('', $conditions);
    }

    /**
     * Build the GROUP BY clause.
     *
     * @return string
     */
    private function buildGroupBy(): string {
        if (empty($this->groupBy)) {
            return '';
        }

        $quotedColumns = array_map(fn($col) => $this->grammar->quoteIdentifier($col), $this->groupBy);

        return ' GROUP BY ' . implode(', ', $quotedColumns);
    }

    /**
     * Build the HAVING clause.
     *
     * @return string
     */
    private function buildHaving(): string {
        if (empty($this->having)) {
            return '';
        }

        $sql = ' HAVING ';
        $conditions = array_map(function($having) {
            $quotedColumn = $this->grammar->quoteIdentifier($having['column']);
            $this->bindings[] = $this->grammar->wrapValue($having['value']);

            return $quotedColumn . ' ' . $having['operator'] . ' ?';
        }, $this->having);

        return $sql . implode(' AND ', $conditions);
    }

    /**
     * Build the ORDER BY clause.
     *
     * @return string
     */
    private function buildOrderBy(): string {
        if (empty($this->orderBy)) {
            return '';
        }

        $orders = array_map(function($order) {
            $quotedColumn = $this->grammar->quoteIdentifier($order['column']);

            return $quotedColumn . ' ' . $order['direction']->value;
        }, $this->orderBy);

        return ' ORDER BY ' . implode(', ', $orders);
    }

    /**
     * Build the LIMIT and OFFSET clauses.
     *
     * @return string
     */
    private function buildLimit(): string {
        if ($this->limit === null) {
            return '';
        }

        return $this->grammar->compileLimit($this->limit, $this->offset);
    }
    #endregion Build Statement Properties

    #region Database Driver
    /**
     * Set the database driver and initialize the grammar.
     *
     * @param DatabaseDriver $driver
     *
     * @return self
     */
    public function setDriver(DatabaseDriver $driver): self {
        $this->grammar = match ($driver) {
            DatabaseDriver::MYSQL => new MySQLGrammar(),
            DatabaseDriver::POSTGRESQL => new PostgreSQLGrammar(),
            DatabaseDriver::SQLITE => new SQLiteGrammar(),
            DatabaseDriver::ANSI => new ANSIGrammar(),
        };

        return $this;
    }

    /**
     * Get the database grammar.
     *
     * @return DatabaseGrammar
     */
    public function getGrammar(): DatabaseGrammar {
        return $this->grammar;
    }
    #endregion Database Driver

    /**
     * Set the table which the query is targeting.
     *
     * @param string $table
     *
     * @return self
     */
    public function table(string $table): self {
        $this->table = $table;

        return $this;
    }

    #region Query Type
    /**
     * Set the columns to be selected.
     *
     * @param string|array ...$columns
     *
     * @return self
     */
    public function select(string|array ...$columns): self {
        $this->type = QueryType::SELECT;
        $this->columns = empty($columns) ? ['*'] : (is_array($columns[0]) ? $columns[0] : $columns);

        return $this;
    }

    /**
     * Set the query type to INSERT and set the data.
     *
     * @param array $data
     *
     * @return self
     */
    public function insert(array $data): self {
        $this->type = QueryType::INSERT;
        $this->data = $data;

        return $this;
    }

    /**
     * Set the query type to UPDATE and set the data.
     *
     * @param array $data
     *
     * @return self
     */
    public function update(array $data): self {
        $this->type = QueryType::UPDATE;
        $this->data = $data;

        return $this;
    }

    /**
     * Set the query type to DELETE.
     *
     * @return self
     */
    public function delete(): self {
        $this->type = QueryType::DELETE;

        return $this;
    }
    #endregion Query Type

    #region Where Clauses
    /**
     * Add a basic where clause from an array.
     *
     * @param array $condition
     *
     * @return void
     */
    private function addBasicWhereFromArray(array $condition): void {
        $count = count($condition);

        if ($count < 2 || $count > 4) {
            throw new \InvalidArgumentException('Condition array must have 2-4 elements: [column, value] or [column, operator, value] or [column, operator, value, boolean]');
        }

        // Extract values with defaults
        $column = $condition[0];
        $operator = $count === 2 ? '=' : $condition[1];
        $value = $count === 2 ? $condition[1] : $condition[2];
        $boolean = $condition[3] ?? 'AND';

        $this->wheres[] = new WhereClause(type: 'basic', column: $column, operator: $operator, value: $value, boolean: strtoupper($boolean));
    }

    /**
     * Add a where clause by type from an array.
     *
     * @param array $condition
     *
     * @return void
     */
    private function addWhereClauseByType(array $condition): void {
        $type = $condition['type'];
        $column = $condition['column'] ?? null;
        $boolean = strtoupper($condition['boolean'] ?? 'AND');

        if ($column === null && !in_array($type, ['raw'])) {
            throw new \InvalidArgumentException('Column is required for where clause');
        }

        match ($type) {
            'basic' => $this->wheres[] = new WhereClause(type: 'basic', column: $column, operator: $condition['operator'] ?? '=', value: $condition['value'] ?? null, boolean: $boolean),

            'in' => $this->wheres[] = new WhereClause(type: 'in', column: $column, boolean: $boolean, values: $condition['values'] ?? []),

            'null' => $this->wheres[] = new WhereClause(type: 'null', column: $column, boolean: $boolean),

            'not_null' => $this->wheres[] = new WhereClause(type: 'not_null', column: $column, boolean: $boolean),

            'like' => $this->wheres[] = new WhereClause(type: 'basic', column: $column, operator: 'LIKE', value: $condition['value'] ?? null, boolean: $boolean),

            'between' => $this->wheres[] = new WhereClause(type: 'between', column: $column, boolean: $boolean, values: $condition['values'] ?? []),

            default => throw new \InvalidArgumentException("Unsupported where type: $type")
        };
    }

    /**
     * Add a basic where clause to the query.
     *
     * @param string $column
     * @param mixed $operator
     * @param mixed $value
     *
     * @return self
     */
    public function where(string $column, mixed $operator = null, mixed $value = null): self {
        [$operator, $value] = match (func_num_args()) {
            2 => ['=', $operator],
            default => [$operator, $value]
        };

        $this->wheres[] = new WhereClause(type: 'basic', column: $column, operator: $operator, value: $value, boolean: 'AND');

        return $this;
    }

    /**
     * Add multiple where clauses to the query.
     *
     * @param array $conditions
     *
     * @return self
     */
    public function wheres(array $conditions): self {
        foreach($conditions as $condition) {
            if (!is_array($condition)) {
                throw new \InvalidArgumentException('Each condition must be an array');
            }
            // Check if it's an associative array with 'type' key
            if (isset($condition['type'])) {
                $this->addWhereClauseByType($condition);
            } else {
                // Legacy numeric array format for basic where
                $this->addBasicWhereFromArray($condition);
            }
        }

        return $this;
    }

    /**
     * Add an OR where clause to the query.
     *
     * @param string $column
     * @param mixed $operator
     * @param mixed $value
     *
     * @return self
     */
    public function orWhere(string $column, mixed $operator = null, mixed $value = null): self {
        [$operator, $value] = match (func_num_args()) {
            2 => ['=', $operator],
            default => [$operator, $value]
        };

        $this->wheres[] = new WhereClause(type: 'basic', column: $column, operator: $operator, value: $value, boolean: 'OR');

        return $this;
    }

    /**
     * Add a WHERE IN clause to the query.
     *
     * @param string $column
     * @param array $values
     *
     * @return self
     */
    public function whereIn(string $column, array $values): self {
        $this->wheres[] = new WhereClause(type: 'in', column: $column, boolean: 'AND', values: $values);

        return $this;
    }

    /**
     * Add a WHERE IS NULL clause to the query.
     *
     * @param string $column
     *
     * @return self
     */
    public function whereNull(string $column): self {
        $this->wheres[] = new WhereClause(type: 'null', column: $column, boolean: 'AND');

        return $this;
    }

    /**
     * Add a WHERE IS NOT NULL clause to the query.
     *
     * @param string $column
     *
     * @return self
     */
    public function whereNotNull(string $column): self {
        $this->wheres[] = new WhereClause(type: 'not_null', column: $column, boolean: 'AND');

        return $this;
    }

    /**
     * Add a WHERE LIKE clause to the query.
     *
     * @param string $column
     * @param string $value
     *
     * @return self
     */
    public function whereLike(string $column, string $value): self {
        $this->wheres[] = new WhereClause(type: 'basic', column: $column, operator: 'LIKE', value: $value, boolean: 'AND');

        return $this;
    }

    /**
     * Add a WHERE BETWEEN clause to the query.
     *
     * @param string $column
     * @param array $values
     *
     * @return self
     */
    public function whereBetween(string $column, array $values): self {
        if (count($values) !== 2) {
            throw new \InvalidArgumentException('whereBetween requires exactly 2 values');
        }

        $this->wheres[] = new WhereClause(type: 'between', column: $column, boolean: 'AND', values: $values);

        return $this;
    }
    #endregion Where Clauses

    #region Join Clauses
    /**
     * Add a JOIN clause to the query.
     *
     * @param string $table
     * @param string $first
     * @param string|null $operator
     * @param string|null $second
     *
     * @return self
     */
    public function join(
        string $table, string $first, ?string $operator = null, ?string $second = null,
    ): self {
        [$operator, $second] = match (func_num_args()) {
            3 => ['=', $operator],
            default => [$operator, $second]
        };

        $this->joins[] = new JoinClause(type: JoinType::INNER, table: $table, first: $first, operator: $operator, second: $second);

        return $this;
    }

    /**
     * Add a LEFT JOIN clause to the query.
     *
     * @param string $table
     * @param string $first
     * @param string|null $operator
     * @param string|null $second
     *
     * @return self
     */
    public function leftJoin(
        string $table, string $first, ?string $operator = null, ?string $second = null,
    ): self {
        [$operator, $second] = match (func_num_args()) {
            3 => ['=', $operator],
            default => [$operator, $second]
        };

        $this->joins[] = new JoinClause(type: JoinType::LEFT, table: $table, first: $first, operator: $operator, second: $second);

        return $this;
    }

    /**
     * Add a RIGHT JOIN clause to the query.
     *
     * @param string $table
     * @param string $first
     * @param string|null $operator
     * @param string|null $second
     *
     * @return self
     */
    public function rightJoin(
        string $table, string $first, ?string $operator = null, ?string $second = null,
    ): self {
        [$operator, $second] = match (func_num_args()) {
            3 => ['=', $operator],
            default => [$operator, $second]
        };

        $this->joins[] = new JoinClause(type: JoinType::RIGHT, table: $table, first: $first, operator: $operator, second: $second);

        return $this;
    }
    #endregion Join Clauses

    #region Group Clauses
    /**
     * Add an ORDER BY clause to the query.
     *
     * @param string $column
     * @param OrderDirection|string $direction
     *
     * @return self
     */
    public function orderBy(string $column, OrderDirection|string $direction = OrderDirection::ASC): self {
        $dir = $direction instanceof OrderDirection ? $direction : OrderDirection::from(strtoupper($direction));

        $this->orderBy[] = [
            'column'    => $column,
            'direction' => $dir,
        ];

        return $this;
    }

    /**
     * Add a GROUP BY clause to the query.
     *
     * @param string|array ...$columns
     *
     * @return self
     */
    public function groupBy(string|array ...$columns): self {
        $this->groupBy = is_array($columns[0]) ? $columns[0] : $columns;

        return $this;
    }

    /**
     * Add a HAVING clause to the query.
     *
     * @param string $column
     * @param string $operator
     * @param mixed $value
     *
     * @return self
     */
    public function having(string $column, string $operator, mixed $value): self {
        $this->having[] = [
            'column'   => $column,
            'operator' => $operator,
            'value'    => $value,
        ];

        return $this;
    }
    #endregion Group Clauses

    #region Limit Clauses
    /**
     * Set the limit for the query.
     *
     * @param int $limit
     *
     * @return self
     */
    public function limit(int $limit): self {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Set the offset for the query.
     *
     * @param int $offset
     *
     * @return self
     */
    public function offset(int $offset): self {
        $this->offset = $offset;

        return $this;
    }
    #endregion Limit Clauses

    #region Output
    /**
     * Convert the query to its SQL representation.
     *
     * @return string
     */
    public function toSql(): string {
        if ($this->table === null) {
            throw new \RuntimeException('Table not specified');
        }

        if ($this->type === null) {
            throw new \RuntimeException('Query type not specified');
        }

        $this->bindings = [];

        return match ($this->type) {
            QueryType::SELECT => $this->buildSelect(),
            QueryType::INSERT => $this->buildInsert(),
            QueryType::UPDATE => $this->buildUpdate(),
            QueryType::DELETE => $this->buildDelete(),
        };
    }

    /**
     * Get the query bindings.
     *
     * @return array
     */
    public function getBindings(): array {
        return $this->bindings;
    }
    #endregion Output
}
