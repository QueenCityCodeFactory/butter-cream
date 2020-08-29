<?php
declare(strict_types=1);

namespace ButterCream\Utility;

/**
 * Formatting Class
 */
class Format
{
    /**
     * Formats a U.S. Social Security Number
     *
     * @param string $ssn The unformatted ssn to format
     * @param string $format The format to be applied
     * @return string The formatted ssn
     */
    public static function ssn(string $ssn, string $format = '000-00-0000'): string
    {
        return self::formatString(self::_stripNonAlphaNumeric($ssn), $format);
    }

    /**
     * Format a 5 or 9 digit U.S. Zipcode
     *
     * @param string $zip The zipcode to be formatted
     * @param array $formats An array of formats where the index is the number of digits present in the zipcode
     * @return string The formatted zipcode
     */
    public static function zip(string $zip, array $formats = []): string
    {
        $formats += [
            5 => '00000',
            9 => '00000-0000',
        ];

        $zip = self::_stripNonAlphaNumeric($zip);

        if (empty($formats[strlen($zip)])) {
            return '';
        }

        return self::formatString($zip, $formats[strlen($zip)]);
    }

    /**
     * Format a phone number to the supplied format string
     *
     * @param string $phone The phone number to be formatted
     * @param array $formats An array of formats where the index is the number of digits present in the phone number
     * @param string $extFormat The ext format
     * @return string The Formatted Phone
     */
    public static function phone(string $phone, array $formats = [], string $extFormat = ' x'): string
    {
        $formats += [
            7 => '000-0000',
            10 => '(000) 000-0000',
        ];

        $phone = self::parsePhone($phone, true);
        $format = !empty($formats[strlen($phone['string'])]) ? $formats[strlen($phone['string'])] : '';
        $formattedPhone = self::formatString($phone['string'], $format);

        if (!empty($phone['parts']['ext'])) {
            $formattedPhone .= $extFormat . $phone['parts']['ext'];
        }

        return $formattedPhone;
    }

    /**
     * Parse the phone number parts from the supplied phone string
     *
     * @param string $phone The phone number to be parsed apart
     * @param bool $returnBoth Whether or not an array containing both the phone string and phone parts is returned
     * @return string|array Defaults to returning a string(NO ext included) of an array with both string and parts
     */
    public static function parsePhone(string $phone, bool $returnBoth = false)
    {
        $rx = '/^.*?(\d{3})?[^\d]*(\d{3})[^\d]*(\d{4})\D*(\d{1,8})?.*$/';

        $parts = [];
        $partsString = '';

        if (preg_match($rx, $phone, $matches)) {
            $parts = [
                'area' => !empty($matches[1]) ? $matches[1] : '',
                'exchange' => !empty($matches[2]) ? $matches[2] : '',
                'number' => !empty($matches[3]) ? $matches[3] : '',
            ];
            $partsString = implode('', $parts);
            $parts['ext'] = !empty($matches[4]) ? $matches[4] : '';
        }

        if ($returnBoth) {
            return [
                'parts' => $parts,
                'string' => $partsString,
            ];
        }

        return $partsString;
    }

    /**
     * Formats a number by injecting non-numeric characters in a specified
     * format into the string in the positions they appear in the format.
     *
     * @param string $string The string to be formatted
     * @param string $format The format to be applied
     * @return string The formatted string
     */
    public static function formatString(string $string, string $format): string
    {
        if (empty($format) || empty($string)) {
            return $string;
        }

        $result = '';
        $fpos = $spos = 0;
        $length = strlen($format) - 1;

        while ($length >= $fpos) {
            if (ctype_alnum(substr($format, $fpos, 1))) {
                $result .= substr($string, $spos, 1);
                $spos++;
            } else {
                $result .= substr($format, $fpos, 1);
            }
             $fpos++;
        }

        return $result;
    }

    /**
     * Transforms a number by masking characters in a specified mask format, and
     * ignoring characters that should be injected into the string without
     * matching a character from the original string (defaults to space).
     *
     * @param string $string The string to be masked
     * @param string $format The format to be applied
     * @param string $ignore Characters to be ignored in the format (Defaults: single space)
     * @return string The masked string
     */
    public static function maskString(string $string = '', string $format = '', string $ignore = ' '): string
    {
        if (empty($format) || empty($string)) {
            return $string;
        }

        $result = '';
        $fpos = $spos = 0;
        $length = strlen($format) - 1;

        while ($length >= $fpos) {
            if (ctype_alnum(substr($format, $fpos, 1))) {
                $result .= substr($string, $spos, 1);
                $spos++;
            } else {
                $result .= substr($format, $fpos, 1);
                if (strpos($ignore, substr($format, $fpos, 1)) === false) {
                    ++$spos;
                }
            }
            ++$fpos;
        }

        return $result;
    }

    /**
     * Strip non alphanumeric characters from the provided string
     *
     * @param string $string The string to remove chars from
     * @return string The string with chars removed
     */
    protected static function _stripNonAlphaNumeric(string $string): string
    {
        return preg_replace('/[^0-9]/', '', $string);
    }
}
