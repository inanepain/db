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

namespace Inane\Db\Adapter\Driver;

use Inane\Db\Query\QueryBuilderInterface;
use Inane\Db\Sql\SQLQueryBuilderInterface;

/**
 * Driver Interface
 *
 * @version 1.0.0
 */
interface DriverInterface {
	/**
	 * Retrieves an instance of the SQLQueryBuilderInterface.
	 *
	 * @return SQLQueryBuilderInterface An instance of a class implementing SQLQueryBuilderInterface.
	 */
	public function getQueryBuilder(): SQLQueryBuilderInterface;

    /**
     * Retrieves an instance of the QueryBuilderInterface.
     *
     * @return QueryBuilderInterface An instance of a class implementing QueryBuilderInterface.
     */
    public function queryBuilder(): QueryBuilderInterface;
}
