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

use function is_null;
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
