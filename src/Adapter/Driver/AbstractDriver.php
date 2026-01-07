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

use Inane\Db\Query\ANSIQueryBuilder;
use Inane\Db\Query\QueryBuilderInterface;
use Inane\Db\Sql\ANSISQLQueryBuilder;
use Inane\Db\Sql\SQLQueryBuilderInterface;
use PDO;

/**
 * AbstractDriver
 *
 * @version 1.0.0
 */
abstract class AbstractDriver extends PDO implements DriverInterface {
    public function __construct(string $dsn, ?string $username = null, ?string $password = null) {
        parent::__construct($dsn, $username, $password);

        // Set default fetch mode
        $this->setAttribute(static::ATTR_DEFAULT_FETCH_MODE, static::FETCH_CLASS);
    } // __construct

	/**
	 * Returns an instance of SQLQueryBuilderInterface.
	 *
	 * @return SQLQueryBuilderInterface
	 */
	public function getQueryBuilder(): SQLQueryBuilderInterface {
		return new ANSISQLQueryBuilder();
	}

    /**
     * Retrieves an instance of the QueryBuilderInterface.
     *
     * @return QueryBuilderInterface An instance of a class implementing QueryBuilderInterface.
     */
    public function queryBuilder(): QueryBuilderInterface {
        return new ANSIQueryBuilder();
    }
}
