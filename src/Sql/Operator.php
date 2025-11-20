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

namespace Inane\Db\Sql;

use function strcasecmp;

/**
 * Operator
 *
 * @version 1.0.0
 */
enum Operator: string {
    case eq = '=';
    case not = '<>';
    case lt = '<';
    case lte = '<=';
    case gt = '>';
    case gte = '>=';
    case like = 'LIKE';
    case isNot = 'is not';

    /**
     * Example implementation: Try get enum from name
     *
     * @param string $name
     * @param bool   $ignoreCase case insensitive option
     *
     * @return null|static returns the enum or null on failure.
     */
    public static function tryFromName(string $name, bool $ignoreCase = false): ?static {
        foreach (static::cases() as $case)
            if (($ignoreCase && strcasecmp($case->name, $name) == 0) || $case->name === $name)
                return $case;

        return null;
    }
}
