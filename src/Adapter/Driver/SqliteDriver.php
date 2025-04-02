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

/**
 * SqliteDriver
 *
 * @version 1.0.0
 */
class SqliteDriver extends AbstractDriver {
    public function __construct(array $config = []) {
        $dsn = 'sqlite:' . $config['dbname'];

        parent::__construct($dsn);
    } // __construct
}
