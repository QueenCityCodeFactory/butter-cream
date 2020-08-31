<?php
declare(strict_types=1);

namespace ButterCream\Utility;

/**
 * Muddle - The missing array class nobody was looking for
 */
class Muddle
{
    /**
     * Insert into an array using a path
     *
     * @param array $array The array data
     * @param string|array $path The Path
     * @param mixed $value The value to set
     * @param string $separator The separator character
     * @return bool True/False did the value get inserted
     */
    public static function insert(array &$array, $path, $value, string $separator = '.'): bool
    {
        if (!is_array($path)) {
            $path = explode($separator, $path);
        }

        if (empty($path)) {
            return false;
        }

        foreach ($path as $key) {
            $array = &$array[$key];
        }

        $array = $value;

        return true;
    }

    /**
     * Build dot Notation Path
     *
     * @param array $path The array path
     * @param string|array|null $prefix The prefix if it has one
     * @param string|array|null $suffix The suffix if it has one
     * @param string $separator The separator character
     * @return string The separator separated string path
     */
    public static function buildDotNotationPath(array $path, $prefix = null, $suffix = null, string $separator = '.'): string
    {
        if ($prefix !== null && $prefix !== false) {
            if (!is_array($prefix)) {
                $prefix = [$prefix];
            }
            $path = array_merge($prefix, $path);
        }

        if ($suffix !== null && $suffix !== false) {
            if (!is_array($suffix)) {
                $suffix = [$suffix];
            }
            $path = array_merge($path, $suffix);
        }

        return join($separator, $path);
    }
}
