<?php
declare(strict_types=1);

namespace ButterCream\Shell\Helper;

use Cake\Console\Helper;

/**
 * Json shell helper.
 */
class JsonHelper extends Helper
{
    /**
     * Output method - Generate the output for this shell helper.
     *
     * @param mixed $data Array or Object
     * @param bool $pretty Pretty Output
     * @return string|bool - The JSON data | false if invalid
     */
    public function output($data, $pretty = true)
    {
        $res = false;
        if (is_object($data)) {
            $res = json_encode(json_decode($data, true), $pretty === true ? JSON_PRETTY_PRINT : null);
        } elseif (is_array($data)) {
            $res = json_encode($data, $pretty === true ? JSON_PRETTY_PRINT : null);
        }

        return $res;
    }

    /**
     * Convert JSON Object or String to an associative array
     *
     * @param mixed $data - The data to convert to an associative array. Can be an object or a string of JSON data
     * @return array|bool - The JSON data as an associative array | false if invalid
     */
    public function toArray($data)
    {
        $res = false;
        if (is_object($data)) {
            $res = json_decode($data, true);
        } elseif (is_string($data)) {
            $json = json_encode($data);
            if (json_last_error() === JSON_ERROR_NONE) {
                $res = json_decode($data, true);
            }
        }

        return $res;
    }
}
