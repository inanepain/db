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

/**
 * This Concrete Builder is compatible with PostgreSQL. While Postgres is very
 * similar to Mysql, it still has several differences. To reuse the common code,
 * we extend it from the MySQL builder, while overriding some of the building
 * steps.
 */
class PostgresQueryBuilder extends MysqlQueryBuilder {
    /**
     * Among other things, PostgreSQL has slightly different LIMIT syntax.
     */
    public function limit(int $start, ?int $offset = null): SQLQueryBuilderInterface {
        parent::limit($start, $offset);

        $this->query->limit = " LIMIT $start";
        if (!is_null($offset)) $this->query->limit .= " OFFSET $offset";

        return $this;
    }

    // + tons of other overrides...
}
