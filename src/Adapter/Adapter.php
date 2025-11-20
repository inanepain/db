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

namespace Inane\Db\Adapter;

use Inane\Db\Adapter\Driver\DriverInterface;
use Inane\Stdlib\Array\OptionsInterface;
use Inane\Stdlib\Options;

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

    /**
     * Constructor for the Adapter class.
     *
     * @param array|Options|OptionsInterface $config Optional configuration settings for the adapter.
     *                       This array can include various parameters required
     *                       for initializing the adapter.
     */
    public function __construct(array|Options|OptionsInterface $config = []) {
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

    /**
     * Retrieves the driver instance used by the adapter.
     *
     * @return DriverInterface The driver instance implementing the DriverInterface.
     */
    public function getDriver(): DriverInterface {
        return $this->driver;
    } // getDriver
}
