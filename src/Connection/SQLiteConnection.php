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

namespace Inane\Db\Connection;

use PDO;

use function is_null;

/**
 * SQLite connection
 *
 * @version 1.0.0
 */
class SQLiteConnection implements ConnectionInterface {
    /**
     * PDO instance
     *
     * @var null|\PDO
     */
    private ?PDO $pdo;

    /**
     * return in instance of the PDO object that connects to the SQLite database
     *
     * @return \Inane\Db\Connection\SQLiteConnection
     */
    public function connect(): self {
        if (!$this->isConnected())
            $this->pdo = new PDO('sqlite:' . Config::PATH_TO_SQLITE_FILE);

        return $this;
    }

    /**
     * Check connection state
     *
     * @return bool
     */
    public function isConnected(): bool {
        return !is_null($this->pdo);
    }

    /**
     * Close the connection
     *
     * @return \Inane\Db\Connection\SQLiteConnection
     */
    public function disconnect(): self {
        if ($this->isConnected()) $this->pdo = null;
        return $this;
    }
}
