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

/**
 * Connection Interface
 *
 * @version 1.0.0
 */
interface ConnectionInterface {
    /**
     * Connect
     *
     * @return ConnectionInterface
     */
    public function connect(): ConnectionInterface;

    /**
     * Is connected
     *
     * @return bool
     */
    public function isConnected(): bool;

    /**
     * Disconnect
     *
     * @return ConnectionInterface
     */
    public function disconnect(): ConnectionInterface;
}
