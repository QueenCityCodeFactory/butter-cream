<?php
namespace ButterCream\Database\Type;

use Cake\Database\Driver;
use Cake\Database\Type;
use PDO;

/**
 * Provides behavior for the JSON type
 */
class JsonType extends Type
{

    /**
     * Do you want this to be an object or array - we want the object!
     * @var boolean
     */
    public $array = false;

    /**
     * Convert the given value from the database type to PHP object
     *
     * @param mixed $value value to be converted to PHP object
     * @param Driver $driver object from which database preferences and configuration will be extracted
     * @return mixed
     */
    public function toPHP($value, Driver $driver)
    {
        if ($value === null) {
            return null;
        }

        return json_decode($value, $this->array);
    }

    /**
     * Convert the request data into object
     * @param mixed $value Request data
     * @return mixed Converted value.
     */
    public function marshal($value)
    {
        if (is_array($value) || $value === null) {
            return $value;
        }

        return json_decode($value, $this->array);
    }

    /**
     * JSON encode value as text to save in database
     *
     * @param string|array $value  The value to convert.
     * @param Driver $driver The driver instance to convert with.
     * @return string
     */
    public function toDatabase($value, Driver $driver)
    {
        if (!is_array($value) && !is_object($value)) {
            return $value;
        }

        return json_encode($value);
    }

    /**
     * Casts give value to Statement equivalent
     *
     * @param mixed $value value to be converted to PHP equivalent
     * @param Driver $driver object from which database preferences and configuration will be extracted
     * @return mixed
     */
    public function toStatement($value, Driver $driver)
    {
        if ($value === null) {
            return PDO::PARAM_NULL;
        }

        return PDO::PARAM_STR;
    }
}
