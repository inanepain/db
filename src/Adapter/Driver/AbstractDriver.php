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

namespace Inane\Db\Adapter\Driver;

use PDO;

/**
 * AbstractDriver
 *
 * @version 1.0.0
 */
abstract class AbstractDriver extends PDO implements DriverInterface {
    public function __construct(string $dsn, ?string $username = null, ?string $password = null) {
        parent::__construct($dsn, $username, $password);
    } // __construct
}
