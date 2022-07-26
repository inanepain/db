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
class MysqlQueryBuilder extends ANSIQueryBuilder {
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
