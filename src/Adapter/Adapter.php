<?php

/**
 * Inane: Db
 *
 * Inane Database
 *
 * PHP version 8.1
 *
 * @author Philip Michael Raab<philip@cathedral.co.za>
 * @package Inane\Db
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
 */

declare(strict_types=1);

namespace Inane\Db\Adapter;

use Inane\Db\Adapter\Driver\DriverInterface;
use PDOStatement;

/**
 * Adapter
 *
 * @version 1.0.0
 */
class Adapter implements AdapterInterface {
    /**
     * @var DriverInterface The database driver instance
     */
    private DriverInterface $driver;

    public function __construct(array $config = []) {
        // Set the adapter to the appropriate class based on the driver
        if ($config['driver'] == 'sqlite') {
            $this->driver = new \Inane\Db\Adapter\Driver\SqliteDriver($config);
        } elseif ($config['driver'] == 'mysql') {
            $this->driver = new \Inane\Db\Adapter\Driver\MysqlDriver($config);
        } else {
            throw new \Exception('Unsupported driver: ' . $config['driver']);
        }

        // Constructor code can be added here if needed
    } // __construct

    public function getDriver(): DriverInterface {
        return $this->driver;
    } // getDriver
}
