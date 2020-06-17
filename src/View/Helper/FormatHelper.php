<?php
declare(strict_types=1);

namespace ButterCream\View\Helper;

use ButterCream\Utility\Format;
use Cake\View\Helper;

/**
 * Format Helper
 */
class FormatHelper extends Helper
{
    /**
     * Formats a U.S. Social Security Number
     *
     * @param string $ssn The unformatted ssn to format
     * @param string $format The format to be applied
     * @return string The formatted ssn
     * @see \App\Utility\Format::ssn()
     */
    public function ssn(string $ssn, string $format = '000-00-0000'): string
    {
        return Format::ssn($ssn, $format);
    }

    /**
     * Format a 5 or 9 digit U.S. Zipcode
     *
     * @param string $zip The zipcode to be formatted
     * @param array $formats An array of formats where the index is the number of digits present in the zipcode
     * @return string The formatted zipcode
     * @see \App\Utility\Format::zip()
     */
    public function zip(string $zip, array $formats = []): string
    {
        return Format::zip($zip, $formats);
    }

    /**
     * Format a phone number to the supplied format string
     *
     * @param string $phone The phone number to be formatted
     * @param array $formats An array of formats where the index is the number of digits present in the phone number
     * @param string $extFormat The ext format
     * @return string The formatted phone
     * @see \App\Utility\Format::phone()
     */
    public function phone(string $phone, array $formats = [], string $extFormat = ' x'): string
    {
        return Format::phone($phone, $formats, $extFormat);
    }

    /**
     * Parse the phone number parts from the supplied phone string
     *
     * @param string $phone The phone number to be parsed apart
     * @param bool $returnBoth Whether or not an array containing both the phone string and phone parts is returned
     * @return string|array Defaults to returning a string(NO ext included) of an array with both string and parts
     * @see \App\Utility\Format::parsePhone()
     */
    public function parsePhone(string $phone, bool $returnBoth = false)
    {
        return Format::parsePhone($phone, $returnBoth);
    }

    /**
     * Formats a number by injecting non-numeric characters in a specified
     * format into the string in the positions they appear in the format.
     *
     * @param string $string The string to be formatted
     * @param string $format The format to be applied
     * @return string The formatted string
     * @see \App\Utility\Format::formatString()
     */
    public function formatString(string $string, string $format): string
    {
        return Format::formatString($string, $format);
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
     * @see \App\Utility\Format::maskString()
     */
    public function maskString(string $string = '', string $format = '', string $ignore = ' '): string
    {
        return Format::maskString($string, $format, $ignore);
    }
}
