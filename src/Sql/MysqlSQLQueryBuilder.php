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

use function in_array;
use function is_null;
use const null;

/**
 * Each Concrete Builder corresponds to a specific SQL dialect and may implement
 * the builder steps a little bit differently from the others.
 *
 * This Concrete Builder can build SQL queries compatible with MySQL.
 *
 * @version 1.0.0
 */
class MysqlSQLQueryBuilder extends ANSISQLQueryBuilder {
    /**
     * Add a LIMIT constraint.
     */
    public function limit(int $limit, ?int $offset = null): SQLQueryBuilderInterface {
        if (!in_array($this->query->type, ['select']))
            throw new \Exception('LIMIT can only be added to SELECT');

        if (!is_null($offset)) $this->query->limit = " LIMIT {$offset}, {$limit}";
        else $this->query->limit = " LIMIT $limit";

        return $this;
    }
}
