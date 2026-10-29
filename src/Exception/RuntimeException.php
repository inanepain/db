<?php

/**
 * RuntimeException
 *
 * Inane Library
 *
 * $Id$
 * $Date$
 *
 * PHP version 8.5
 *
 * @author   Philip Michael Raab <philip@inane.co.za>
 * @package  inanepain\db
 * @category db
 *
 * @license  UNLICENSE
 * @license  https://unlicense.org/UNLICENSE UNLICENSE
 */

declare(strict_types = 1);

namespace Inane\Db\Exception;

use Throwable;

/**
 * RuntimeException
 *
 * db
 *
 * @version 0.1.0
 */
class RuntimeException extends \Inane\Stdlib\Exception\RuntimeException {
    /**
     * Custom construct template
     *
     * @param string          $message  error description
     * @param int|mixed       $code     unique identifier
     * @param null|\Throwable $previous error if any
     *
     * @return void
     */
    public function __construct(string $message = '', $code = 0, ?Throwable $previous = null) {
        parent::__construct("Inane\Db\Error: $message", $code, $previous);
    }
}
