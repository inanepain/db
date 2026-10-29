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
 * @author   Philip Michael Raab<philip@inane.co.za>
 * @package  inanepain\db
 * @category db
 *
 * @license  UNLICENSE
 * @license  https://unlicense.org/UNLICENSE UNLICENSE
 */

declare(strict_types = 1);

namespace Inane\Db\Adapter\Driver;

use Inane\Db\Query\ANSIQueryBuilder;
use Inane\Db\Query\QueryBuilderInterface;
use PDO;

/**
 * AbstractDriver
 *
 * @version 1.0.0
 */
abstract class AbstractDriver extends PDO implements DriverInterface {
    /**
     * Initialises a new database connection and sets the default fetch mode.
     *
     * @param string      $dsn      The Data Source Name, or DSN, containing the information required to connect to the database.
     * @param string|null $username The username for the database connection. Optional.
     * @param string|null $password The password for the database connection. Optional.
     *
     * @return void
     */
    public function __construct(string $dsn, ?string $username = null, ?string $password = null) {
        parent::__construct($dsn, $username, $password);

        // Set default fetch mode
        $this->setAttribute(static::ATTR_DEFAULT_FETCH_MODE, static::FETCH_CLASS);
    } // __construct

    /**
     * Retrieves an instance of the QueryBuilderInterface.
     *
     * @return QueryBuilderInterface An instance of a class implementing QueryBuilderInterface.
     */
    public function queryBuilder(): QueryBuilderInterface {
        return new ANSIQueryBuilder();
    }
}
