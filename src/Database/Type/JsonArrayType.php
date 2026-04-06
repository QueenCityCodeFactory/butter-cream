<?php
declare(strict_types=1);

namespace ButterCream\Database\Type;

use Cake\Database\Driver;
use Cake\Database\Type\BatchCastingInterface;
use Cake\Database\Type\JsonType;

/**
 * JSON database type that decodes values as objects instead of associative arrays.
 *
 * Extends CakePHP's `JsonType` to use `json_decode($value, false)`, returning
 * `stdClass` objects for JSON objects. This is useful when you need to distinguish
 * between an empty object (`{}`) and an empty array (`[]`), or when object property
 * access (`$data->name`) is preferred over array key access (`$data['name']`).
 *
 * Also implements `BatchCastingInterface` for efficient bulk decoding of multiple
 * rows via `manyToPHP()`.
 *
 * ### Usage
 *
 * Register the type in your application's `bootstrap.php`:
 *
 * ```php
 * use Cake\Database\TypeFactory;
 * TypeFactory::map('json_array', JsonArrayType::class);
 * ```
 *
 * Then in your Table class:
 *
 * ```php
 * $schema->setColumnType('metadata', 'json_array');
 * ```
 */
class JsonArrayType extends JsonType implements BatchCastingInterface
{
    /**
     * Convert a JSON string from the database into a PHP value (object).
     *
     * Returns `null` if the value is not a string or if JSON decoding fails.
     * Unlike the parent `JsonType`, this decodes JSON objects as `stdClass`
     * instances rather than associative arrays.
     *
     * @param mixed $value The raw database value.
     * @param \Cake\Database\Driver $driver The database driver instance.
     * @return mixed The decoded value, or `null` on failure.
     */
    public function toPHP(mixed $value, Driver $driver): mixed
    {
        if (!is_string($value)) {
            return null;
        }

        $decoded = json_decode($value, false);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }

    /**
     * Batch-convert multiple JSON columns to PHP values in a single row.
     *
     * Iterates over the specified fields and decodes each JSON string as an object.
     * Fields that are not set in the row are skipped. Invalid JSON is replaced with `null`.
     *
     * @param array<array-key, mixed> $values The row data keyed by column name.
     * @param array<array-key, mixed> $fields The column names to decode.
     * @param \Cake\Database\Driver $driver The database driver instance.
     * @return array<string, mixed> The row data with decoded JSON values.
     */
    public function manyToPHP(array $values, array $fields, Driver $driver): array
    {
        foreach ($fields as $field) {
            if (!isset($values[$field])) {
                continue;
            }

            $decoded = json_decode($values[$field], false);
            $values[$field] = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        }

        return $values;
    }
}
