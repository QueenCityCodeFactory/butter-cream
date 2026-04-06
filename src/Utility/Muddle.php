<?php
declare(strict_types=1);

namespace ButterCream\Utility;

/**
 * Muddle - Utility class for working with nested arrays using dot-notation paths.
 *
 * Provides helpers to set deeply nested values and build dot-notation path strings,
 * useful for working with CakePHP configuration arrays, form field names, and
 * any structure that uses separator-delimited keys.
 *
 * ### Examples
 *
 * ```php
 * // Set a nested value using dot-notation
 * $data = [];
 * Muddle::insert($data, 'user.address.city', 'Cincinnati');
 * // $data = ['user' => ['address' => ['city' => 'Cincinnati']]]
 *
 * // Build a dot-notation path from parts
 * $path = Muddle::buildDotNotationPath(['street'], 'user.address', 'zip');
 * // $path = 'user.address.street.zip'
 * ```
 */
class Muddle
{
    /**
     * Insert a value into a nested array using a dot-notation path.
     *
     * Traverses (and creates) nested array keys by splitting the path on the
     * separator, then sets the final key to the given value. The array is
     * modified in place by reference.
     *
     * ### Examples
     *
     * ```php
     * $config = [];
     * Muddle::insert($config, 'database.host', 'localhost');
     * // $config = ['database' => ['host' => 'localhost']]
     *
     * // Using an array path instead of a string
     * Muddle::insert($config, ['database', 'port'], 3306);
     * // $config = ['database' => ['host' => 'localhost', 'port' => 3306]]
     *
     * // Custom separator
     * $data = [];
     * Muddle::insert($data, 'app/debug', true, '/');
     * // $data = ['app' => ['debug' => true]]
     * ```
     *
     * @param array<string, mixed> $array The array to modify (by reference).
     * @param list<string>|string $path Dot-notation string or pre-split list of keys.
     * @param mixed $value The value to set at the target path.
     * @param non-empty-string $separator The delimiter used to split a string path. Defaults to `.`.
     * @return bool Returns `true` on success, `false` if the path is empty.
     */
    public static function insert(array &$array, string|array $path, mixed $value, string $separator = '.'): bool
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
     * Build a dot-notation path string from an array of segments, with optional prefix and suffix.
     *
     * Merges prefix, path, and suffix segments into a single separator-delimited string.
     * Prefix and suffix can each be a string (which is wrapped in an array) or an array
     * of segments. Pass `null` or `false` to omit either.
     *
     * ### Examples
     *
     * ```php
     * // Basic usage
     * Muddle::buildDotNotationPath(['city', 'zip']);
     * // 'city.zip'
     *
     * // With a string prefix
     * Muddle::buildDotNotationPath(['name'], 'user');
     * // 'user.name'
     *
     * // With array prefix and string suffix
     * Muddle::buildDotNotationPath(['address'], ['user', 'profile'], 'city');
     * // 'user.profile.address.city'
     *
     * // Custom separator for form field names
     * Muddle::buildDotNotationPath(['street'], 'address', null, '/');
     * // 'address/street'
     *
     * // Omit prefix/suffix with false or null
     * Muddle::buildDotNotationPath(['a', 'b'], false, null);
     * // 'a.b'
     * ```
     *
     * @param list<string> $path The base path segments.
     * @param list<string>|string|false|null $prefix Segments to prepend, or `false`/`null` to skip.
     * @param list<string>|string|false|null $suffix Segments to append, or `false`/`null` to skip.
     * @param string $separator The delimiter to join segments with. Defaults to `.`.
     * @return string The fully joined path string.
     */
    public static function buildDotNotationPath(
        array $path,
        string|array|false|null $prefix = null,
        string|array|false|null $suffix = null,
        string $separator = '.',
    ): string {
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
