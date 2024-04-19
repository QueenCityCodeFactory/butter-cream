<?php
declare(strict_types=1);

namespace ButterCream\Database\Type;

use Cake\Database\Driver;
use Cake\Database\Type\BatchCastingInterface;
use Cake\Database\Type\JsonType;

/**
 * Provides behavior for the JSON type
 */
class JsonArrayType extends JsonType implements BatchCastingInterface
{
    /**
     * {@inheritDoc}
     *
     * @param mixed $value The value to convert.
     * @param \Cake\Database\Driver $driver The driver instance to convert with.
     * @return mixed
     */
    public function toPHP(mixed $value, Driver $driver): mixed
    {
        if (!is_string($value)) {
            return null;
        }

        return json_decode($value, false);
    }

    /**
     * @inheritDoc
     */
    public function manyToPHP(array $values, array $fields, Driver $driver): array
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
