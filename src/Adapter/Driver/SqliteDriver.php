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
use Inane\Db\Query\SQLiteQueryBuilder;
use Inane\Db\Sql\SqliteSQLQueryBuilder;
use Inane\Db\Sql\SQLQueryBuilderInterface;
use Inane\Stdlib\Array\OptionsInterface;
use Inane\Stdlib\Options;

/**
 * SqliteDriver
 *
 * @version 1.0.0
 */
class SqliteDriver extends AbstractDriver {
    public function __construct(array|Options|OptionsInterface $config = []) {
        $dsn = 'sqlite:' . $config['dbname'];

        parent::__construct($dsn);
    } // __construct

	/**
	 * Returns an instance of SQLQueryBuilderInterface.
	 *
	 * @return SQLQueryBuilderInterface Returns an instance of SQLQueryBuilderInterface.
	 */
	public function getQueryBuilder(): SQLQueryBuilderInterface {
		return new SqliteSQLQueryBuilder();
	}

    /**
     * Retrieves an instance of the QueryBuilderInterface.
     *
     * @return QueryBuilderInterface An instance of a class implementing QueryBuilderInterface.
     */
    public function queryBuilder(): QueryBuilderInterface {
        return new SQLiteQueryBuilder();
    }
}
