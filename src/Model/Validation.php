<?php
declare(strict_types=1);

namespace ButterCream\Model;

use Cake\I18n\Time;

/**
 * Validation Class. Used for validation of model data
 *
 * Offers different validation methods.
 */
class Validation
{
    /**
     * Checks a phone number for The United States
     *
     * @param string $check The value to check.
     * @return bool Success.
     */
    public static function phone(string $check): bool
    {
        $regex = '/^(?:(?:\+?1\s*(?:[.-]\s*)?)?(?:\(\s*([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9])\s*\)';
        $regex .= '|([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9]))\s*';
        $regex .= '(?:[.-]\s*)?)?([2-9]1[02-9]|[2-9][02-9]1|[2-9][02-9]{2})\s*(?:[.-]\s*)';
        $regex .= '?([0-9]{4})(?:\s*(?:#|x\.?|ext\.?|extension)\s*(\d+))?$/';

        return static::_check($check, $regex);
    }

    /**
     * Checks a postal code for The United States
     *
     * @param string $check The value to check.
     * @return bool Success.
     */
    public static function postal(string $check): bool
    {
        $regex = '/\\A\\b[0-9]{5}(?:-?[0-9]{4})?\\b\\z/i';

        return static::_check($check, $regex);
    }

    /**
     * Checks a social security number for The United States
     *
     * @param string $check The value to check.
     * @return bool Success
     */
    public static function ssn(string $check): bool
    {
        $regex = '/\\A\\b[0-9]{3}-?[0-9]{2}-?[0-9]{4}\\b\\z/i';

        return static::_check($check, $regex);
    }

    /**
     * Checks to make sure the birthdate is not in the future (but can be today's date)
     *
     * @param \Cake\I18n\Time $check The value to check.
     * @return bool Success
     */
    public static function birthdate(Time $check): bool
    {
        $today = new Time('00:00:00');
        if ($check->toUnixString() > $today->toUnixString()) {
            return false;
        }

        return true;
    }

    /**
     * Runs a regular expression match.
     *
     * @param string $check Value to check against the $regex expression
     * @param string $regex Regular expression
     * @return bool Success of match
     */
    protected static function _check(string $check, string $regex): bool
    {
        if (is_string($regex) && preg_match($regex, $check)) {
            return true;
        }

        return false;
    }
}
