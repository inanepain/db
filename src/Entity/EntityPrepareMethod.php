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

namespace Inane\Db\Entity;

use Attribute;
use Exception;
use Inane\Db\Table\AbstractTable;
use Stringable;
use Inane\Stdlib\{
    Converters\Arrayable,
    Json
};

use function array_key_exists;
use function is_null;
use function method_exists;

use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

#[Attribute(Attribute::TARGET_METHOD)]
/**
 * Class EntityPrepareMethod
 *
 * Represents a method used to prepare or process entity data before it is persisted.
 * This class may include logic for sanitizing, validating, or transforming entity properties.
 */
class EntityPrepareMethod {
}