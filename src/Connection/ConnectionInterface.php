<?php

/**
 * Inane: Db
 *
 * Inane Database
 *
 * PHP version 8.1
 *
 * @author Philip Michael Raab<peep@inane.co.za>
 * @package Inane\Db
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
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
