<?php
declare(strict_types=1);

namespace ButterCream\Database\Type;

use Cake\Database\DriverInterface;
use Cake\Database\Type\BatchCastingInterface;
use Cake\Database\Type\JsonType;

/**
 * Provides behavior for the JSON type
 */
class JsonArrayType extends JsonType implements BatchCastingInterface
{
    /**
     * Convert string values to PHP arrays.
     *
     * @param mixed $value The value to convert.
     * @param \Cake\Database\DriverInterface $driver The driver instance to convert with.
     * @return string|array|null
     */
    public function toPHP($value, DriverInterface $driver)
    {
        if (!is_string($value)) {
            return null;
        }

        return json_decode($value, false);
    }

    /**
     * Returns an array of the values converted to the PHP representation of
     * this type.
     *
     * @param array $values The original array of values containing the fields to be casted
     * @param string[] $fields The field keys to cast
     * @param \Cake\Database\DriverInterface $driver Object from which database preferences and configuration will be extracted.
     * @return array
     */
    public function manyToPHP(array $values, array $fields, DriverInterface $driver): array
    {
        foreach ($fields as $field) {
            if (!isset($values[$field])) {
                continue;
            }

            $values[$field] = json_decode($values[$field], false);
        }

        return $values;
    }
}
