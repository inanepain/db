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

use function array_intersect_key;
use function implode;

use const null;

/**
 * MysqlDriver
 *
 * @version 1.0.0
 */
class MysqlDriver extends AbstractDriver {
    public function __construct(array $config = []) {
        $opts = array_intersect_key($config, ['dbname' => '', 'host' => '', 'port' => '', 'unix_socket' => '']);

        $dsn = [];
        foreach ($opts as $key => $value) {
            $dsn[] = $key . '=' . $value;
        };

        parent::__construct('mysql:' . implode(';', $dsn), $config['username'] ?? null, $config['password'] ?? null);
    } // __construct
}
