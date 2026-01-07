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

use Inane\Db\Query\MySQLQueryBuilder;
use Inane\Db\Query\QueryBuilderInterface;
use Inane\Db\Sql\MysqlSQLQueryBuilder;
use Inane\Db\Sql\SQLQueryBuilderInterface;
use Inane\Stdlib\Array\OptionsInterface;
use Inane\Stdlib\Options;
use function array_intersect_key;
use function implode;

/**
 * MysqlDriver
 *
 * @version 1.0.0
 */
class MysqlDriver extends AbstractDriver {
    public function __construct(array|Options|OptionsInterface $config = []) {
        $opts = array_intersect_key($config, ['dbname' => '', 'host' => '', 'port' => '', 'unix_socket' => '']);

        $dsn = [];
        foreach ($opts as $key => $value) {
            $dsn[] = $key . '=' . $value;
        };

        parent::__construct('mysql:' . implode(';', $dsn), $config['username'] ?? null, $config['password'] ?? null);
    } // __construct

	/**
	 * Returns an instance of SQLQueryBuilderInterface.
	 *
	 * @return SQLQueryBuilderInterface
	 */
	public function getQueryBuilder(): SQLQueryBuilderInterface {
		return new MysqlSQLQueryBuilder();
	}

    /**
     * Retrieves an instance of the QueryBuilderInterface.
     *
     * @return QueryBuilderInterface An instance of a class implementing QueryBuilderInterface.
     */
    public function queryBuilder(): QueryBuilderInterface {
        return new MySQLQueryBuilder();
    }
}
