<?php

/**
 * Develop
 *
 * Tinkering development environment. Used to play with or try out stuff.
 *
 * PHP version 8.4
 *
 * @author Philip Michael Raab<philip@cathedral.co.za>
 * @package Develop\Tinker
 *
 * @license UNLICENSE
 * @license https://unlicense.org/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
 */

declare(strict_types=1);

namespace Inane\Db\Entity;

use Exception;
use Inane\Db\Table\AbstractTable;
use Stringable;
use Inane\Stdlib\{
    Converters\Arrayable,
    Json
};

use function array_key_exists;
use function is_null;

use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

/**
 * Abstract class AbstractEntity
 *
 * This class serves as a base class for entities that need to be
 * represented as arrays and strings. It implements the Arrayable
 * and Stringable interfaces.
 */
abstract class AbstractEntity implements Arrayable, Stringable {
    /**
     * @var string $primaryId The primary identifier for the entity, default is 'id'.
     */
    public string $primaryId = 'id';

    /**
     * @var array $data An array to hold the data for the entity.
     */
    protected array $data;

    /**
     * The class name of the data table associated with the entity.
     *
     * @var string
     */
    protected string $dataTableClass;

    /**
     * The data table associated with the entity.
     *
     * @var AbstractTable The table instance that provides data operations for the entity.
     */
    protected AbstractTable $dataTable;

    /**
     * Constructor for the AbstractEntity class.
     *
     * @param array|null $data Optional array of data to initialize the entity.
     */
    public function __construct(?array $data = null, ?AbstractTable $dataTable = null) {
        if (!is_null($data)) $this->updateData($data);
        if (!is_null($dataTable)) $this->dataTable = $dataTable;
        elseif (isset($this->dataTableClass)) $this->dataTable = new $this->dataTableClass;

        if (isset($this->dataTable)) $this->primaryId = $this->dataTable->getPrimaryId();
    }

    /**
     * Retrieves the primary ID of the entity.
     *
     * @return string The primary ID of the entity.
     */
    public function getPrimaryId() : string {
        return $this->primaryId;
    }

    /**
     * Retrieves the value of the primary ID for the entity.
     *
     * @return string|int|float The value of the primary ID, which can be a string, integer, or float.
     */
    public function getPrimaryIdValue() : string|int|float {
        return $this->data[$this->primaryId];
    }

    /**
     * Retrieves an entity by its ID.
     *
     * @param int|string $id The ID of the entity to retrieve. Can be an integer or a string.
     *
     * @return bool Returns true if the entity is successfully retrieved, false otherwise.
     */
    public function fetch(int|string $id) : bool {
        $result = $this->dataTable->fetch($id);

        if ($result !== false) {
            $this->updateData($result->toArray());
            return true;
        }

        return false;
    }

    /**
     * Saves the current entity to the database.
     *
     * This method persists the current state of the entity to the database.
     * It returns a boolean indicating whether the save operation was successful.
     * 
     * @param bool $insert Force insert for tables without auto generated ids.
     *
     * @return bool True if the entity was successfully saved, false otherwise.
     */
    public function save(bool $insert = false) : bool {
        if ($this->getPrimaryIdValue() === null || $insert)
            $result = $this->dataTable->insert($this);
        else
            $result = $this->dataTable->update($this);

        if ($result !== false) {
            $this->updateData($result->toArray());
            return true;
        }

        return false;
    }

    /**
     * Converts the entity to its string representation.
     *
     * @return string The string representation of the entity.
     */
    public function __toString(): string {
        return Json::encode($this->data, ['flags' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE]);
    }

    /**
     * Converts the entity to an array.
     *
     * @return array An associative array representation of the entity.
     */
    public function toArray(): array {
        return $this->getArrayCopy();
    }

    /**
     * Returns an array representation of the entity.
     *
     * @param bool $withPrimary Whether to include the primary key in the array.
     *
     * @return array The array representation of the entity.
     */
    public function getArrayCopy(bool $withPrimary = true): array {
        $data = [];
        foreach ($this->data as $key => $value) {
            if (!$withPrimary && $key == $this->primaryId) continue;
            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * Updates the entity's data with the provided array.
     *
     * @param array $data An associative array containing the data to update.
     *
     * @return void
     */
    protected function updateData(array $data): void {
        if (!isset($this->data)) throw new Exception('Error: property $data not defined.');

        foreach($this->data as $key => $value) {
            if (array_key_exists($key, $data)) {
                $this->$key = $data[$key];
            }
        }
    }
}
