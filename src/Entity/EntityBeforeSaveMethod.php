<?php

/**
 * Inane: Db
 *
 * Some helpers for database task and query construction.
 *
 * $Id$
 * $Date$
 *
 * PHP version 8.5
 *
 * @author   Philip Michael Raab<philip@inane.co.za>
 * @package  inanepain\db
 * @category db
 *
 * @license  UNLICENSE
 * @license  https://unlicense.org/UNLICENSE UNLICENSE
 */

declare(strict_types = 1);

namespace Inane\Db\Entity;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
/**
 * Class EntityBeforeSaveMethod
 *
 * Represents a method used to prepare or process entity data before it is persisted.
 * This class may include logic for sanitising, validating, or transforming entity properties.
 */
class EntityBeforeSaveMethod {}
