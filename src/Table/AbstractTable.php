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

namespace Inane\Db\Table;

use Inane\Db\Entity\AbstractEntity;
use PDO;
use Inane\Db\Adapter\{
    Adapter,
    AdapterInterface
};
use Inane\Stdlib\Array\OptionsInterface;

use function array_key_exists;
use function array_keys;
use function implode;
use function intval;
use function is_numeric;

use const false;
use const null;

/**
 * AbstractTable
 *
 * Abstract class representing a database table.
 *
 * This class provides a base for all database table classes,
 * defining common functionality and structure.
 * 
 * // TODO: version bump
 */
abstract class AbstractTable {
    /**
     * @var AdapterInterface $db The PDO instance for database connection.
     */
    public static AdapterInterface $db;

    /**
     * @var array $statement An array to hold prepared statements.
     */
    protected array $statement = [];

    /**
     * @var string $table The name of the database table associated with this class.
     */
    protected string $table;

    /**
     * @var string $primaryId The primary identifier for the table.
     */
    protected string $primaryId;

    /**
     * Indicates whether the primary key of the table should auto-increment.
     *
     * @var bool
     */
    protected bool $autoIncrement = true;

    /**
     * @var string $entityClass The class name associated with a table record.
     */
    protected string $entityClass;

    /**
     * Constructor for the AbstractTable class.
     *
     * @param OptionsInterface|array|null $config Optional array of data to initialize the entity.
     */
    public function __construct(null|array|OptionsInterface $config = null) {
        if (!isset(static::$db) && $config !== null) {
            static::$db = new Adapter($config);
        }
    }

    #region database table methods

    /**
     * Retrieves the primary ID of the table.
     *
     * @return string The primary ID as a string.
     */
    public function getPrimaryId(): string {
        return $this->primaryId;
    }

    /**
     * Fetches all records from the database table.
     *
     * @return array|AbstractEntity[] An array of all records.
     */
    public function fetchAll(): array {
        if (!array_key_exists(__FUNCTION__, $this->statement))
            $this->statement[__FUNCTION__] = static::$db->getDriver()->prepare('SELECT * FROM `' . $this->table . '`');

        $stmt = $this->statement[__FUNCTION__];
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, $this->entityClass, [null, $this]);
    }

    /**
     * Retrieves an entity by its ID.
     *
     * @param string|int|float $id The ID of the entity to retrieve.
     *
     * @return false|AbstractEntity The entity corresponding to the given ID, or false on failure.
     */
    public function fetch(string|int|float $id): false|AbstractEntity {
        if (!array_key_exists(__FUNCTION__, $this->statement))
            $this->statement[__FUNCTION__] = static::$db->getDriver()->prepare('SELECT * FROM `' . $this->table . '` where ' . $this->primaryId . ' = :' . $this->primaryId);

        $stmt = $this->statement[__FUNCTION__];
        $stmt->execute([':' . $this->primaryId => $id]);

        $result = $stmt->fetchAll(PDO::FETCH_CLASS, $this->entityClass, [null, $this]);
        if (empty($result)) return false;
        else return $result[0];
    }

    /**
     * Searches the database based on the given query.
     *
     * @param array|string $query The search query, which can be either an array or a string.
     *
     * @return array|AbstractEntity[] The search results as an array.
     */
    public function search(array|string $query): array {
        $data = [];
        if (!\is_string($query)) {
            $sql = 'SELECT * FROM `' . $this->table . '` WHERE ';
            foreach ($query as $key => $value) {
                $data[":$key"] = $value;
                $sql .= "`$key` like :$key AND ";
            }
            $sql = substr($sql, 0, -4);
        } else {
            $sql = 'SELECT * FROM `' . $this->table . '` WHERE ' . $query;
        }
        $stmt = static::$db->getDriver()->prepare($sql);
        $stmt->execute($data);
        return $stmt->fetchAll(PDO::FETCH_CLASS, $this->entityClass, [null, $this]);
    }

    /**
     * Inserts a new entity into the database on conflict it updates
     *
     * @param AbstractEntity $entity The entity to insert or update.
     *
     * @return false|AbstractEntity Returns the entity on success, or false on failure.
     */
    public function insertUpdate(AbstractEntity $entity): false|AbstractEntity {
        $array = $entity->getArrayCopy(true);
        if (!array_key_exists(__FUNCTION__, $this->statement)) {
            $insKeys = $updKeys = [];
            foreach (array_keys($array) as $key) {
                if ($key !== $this->primaryId) $updKeys[] = "$key = excluded.$key";
                $insKeys[] = $key;
            }

            $sql = 'INSERT INTO `' . $this->table . '` ("' . implode('", "', $insKeys) . '") VALUES (:' . implode(', :', $insKeys) . ') ON CONFLICT(' . $this->primaryId . ') DO UPDATE SET ' . implode(', ', $updKeys) . ';';
            $this->statement[__FUNCTION__] = static::$db->getDriver()->prepare($sql);
        }

        $data = [];
        foreach ($array as $key => $value) {
            $data[":$key"] = $value;
        }

        $stmt = $this->statement[__FUNCTION__];
        if ($stmt->execute($data) === false)
            return false;

        $id = static::$db->getDriver()->lastInsertId();
        $id = is_numeric($id) ? intval($id) : $id;

        return $this->fetch($id);
    }

    /**
     * Inserts a new entity into the database.
     *
     * @param AbstractEntity $entity The entity to be inserted.
     *
     * @return false|AbstractEntity Returns the inserted entity on success, or false on failure.
     */
    public function insert(AbstractEntity $entity): false|AbstractEntity {
        $array = $entity->getArrayCopy(true);
        if (!array_key_exists(__FUNCTION__, $this->statement)) {
            $keys = array_keys($array);

            $sql = 'INSERT INTO `' . $this->table . '` ("' . implode('", "', $keys) . '") VALUES (:' . implode(', :', $keys) . ')';
            $this->statement[__FUNCTION__] = static::$db->getDriver()->prepare($sql);
        }

        $data = [];
        foreach ($array as $key => $value) {
            $data[":$key"] = $value;
        }

        $stmt = $this->statement[__FUNCTION__];
        if ($stmt->execute($data) === false)
            return false;

        $id = static::$db->getDriver()->lastInsertId();
        $id = is_numeric($id) ? intval($id) : $id;

        return $this->fetch($id);
    }

    /**
     * Updates the given entity in the database.
     *
     * @param AbstractEntity $entity The entity to be updated.
     *
     * @return false|AbstractEntity Returns the updated entity on success, or false on failure.
     */
    public function update(AbstractEntity $entity): false|AbstractEntity {
        $array = $entity->getArrayCopy(false);
        if (!array_key_exists(__FUNCTION__, $this->statement)) {

            $keys = [];
            foreach ($array as $key => $value) {
                $keys[] = "`$key` = :$key";
            }

            $keys = implode(', ', $keys);

            $sql  = "UPDATE `" . $this->table . "` SET $keys WHERE " . $entity->getPrimaryId() . " = :" . $entity->getPrimaryId();
            $this->statement[__FUNCTION__] = static::$db->getDriver()->prepare($sql);
        }

        $stmt = $this->statement[__FUNCTION__];

        $data = [];
        foreach ($array as $key => $value) {
            $data[":$key"] = $value;
        }
        $data[':' . $entity->getPrimaryId()] = $entity->getPrimaryIdValue();

        return $stmt->execute($data) === false ? false : $this->fetch($entity->getPrimaryIdValue());
    }

    /**
     * Deletes the given entity from the database.
     *
     * @param AbstractEntity $entity The entity to be deleted.
     *
     * @return bool Returns true on success, false on failure.
     */
    public function delete(AbstractEntity $entity): bool {
        if (!array_key_exists(__FUNCTION__, $this->statement)) {
            $sql  = "DELETE FROM `" . $this->table . "` WHERE " . $entity->getPrimaryId() . " = :" . $entity->getPrimaryId();
            $this->statement[__FUNCTION__] = static::$db->getDriver()->prepare($sql);
        }

        $stmt = $this->statement[__FUNCTION__];
        return $stmt->execute([':' . $entity->getPrimaryId() => $entity->getPrimaryIdValue()]);
    }

    #endregion
}
