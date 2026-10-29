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
 * @author Philip Michael Raab<philip@inane.co.za>
 * @package inanepain\db
 * @category db
 *
 * @license UNLICENSE
 * @license https://unlicense.org/UNLICENSE UNLICENSE
 */

declare(strict_types=1);

namespace Inane\Db\Adapter\Driver;

use Inane\Db\Query\QueryBuilderInterface;
use Inane\Db\Query\SQLiteQueryBuilder;
use Inane\Stdlib\Array\OptionsInterface;
use Inane\Stdlib\Options;

/**
 * SqliteDriver
 *
 * @version 1.0.0
 */
class SqliteDriver extends AbstractDriver {
    /**
     * Constructor method for initialising the database connection.
     *
     * @param array|Options|OptionsInterface $config Configuration data, including database name under the key 'dbname'.
     *
     * @return void
     */
    public function __construct(array|Options|OptionsInterface $config = []) {
        $dsn = 'sqlite:' . $config['dbname'];

        parent::__construct($dsn);
    } // __construct

    /**
     * Retrieves an instance of the QueryBuilderInterface.
     *
     * @return QueryBuilderInterface An instance of a class implementing QueryBuilderInterface.
     */
    public function queryBuilder(): QueryBuilderInterface {
        return new SQLiteQueryBuilder();
    }
}
