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

use Inane\Db\Query\MySQLQueryBuilder;
use Inane\Db\Query\QueryBuilderInterface;
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
    /**
     * Constructor method for initialising a database connection.
     *
     * @param array|Options|OptionsInterface $config Configuration options for the database connection.
     *                                               Expected keys include:
     *                                               - 'dbname': (string) The name of the database.
     *                                               - 'host': (string) The database host.
     *                                               - 'port': (int) The port number.
     *                                               - 'unix_socket': (string) The UNIX socket.
     *                                               - 'username': (string) The username for database authentication (optional).
     *                                               - 'password': (string) The password for database authentication (optional).
     *
     * @return void
     */
    public function __construct(array|Options|OptionsInterface $config = []) {
        $opts = array_intersect_key($config, [
            'dbname'      => '',
            'host'        => '',
            'port'        => '',
            'unix_socket' => '',
        ]);

        $dsn = [];
        foreach($opts as $key => $value) {
            $dsn[] = $key . '=' . $value;
        };

        parent::__construct('mysql:' . implode(';', $dsn), $config['username'] ?? null, $config['password'] ?? null);
    } // __construct

    /**
     * Retrieves an instance of the QueryBuilderInterface.
     *
     * @return QueryBuilderInterface An instance of a class implementing QueryBuilderInterface.
     */
    public function queryBuilder(): QueryBuilderInterface {
        return new MySQLQueryBuilder();
    }
}
