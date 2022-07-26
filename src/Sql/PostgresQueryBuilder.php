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

use ArrayObject;

use function implode;
use function in_array;
use function is_int;
use function is_null;
use function serialize;
use const null;

/**
 * This Concrete Builder is compatible with PostgreSQL. While Postgres is very
 * similar to ANSI SQL, it still has several differences. To reuse the common code,
 * we extend it from the ANSI builder, while overriding some of the building
 * steps.
 *
 * @version 1.0.0
 */
class PostgresQueryBuilder extends ANSIQueryBuilder {
    /**
     * Among other things, PostgreSQL has slightly different LIMIT syntax.
     */
    public function limit(int $limit, ?int $offset = null): SQLQueryBuilderInterface {
        parent::limit($limit, $offset);

        $this->query->limit = " LIMIT $limit";
        if (!is_null($offset)) $this->query->limit .= " OFFSET $offset";

        return $this;
    }

    // + tons of other overrides...
}
