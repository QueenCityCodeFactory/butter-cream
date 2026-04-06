<?php
declare(strict_types=1);

namespace ButterCream\View\Helper;

use Cake\I18n\DateTime;
use Cake\View\Helper\TimeHelper as Helper;
use DateTimeInterface;
use DateTimeZone;
use Exception;

/**
 * TimeHelper Class
 */
class TimeHelper extends Helper
{
    /**
     * Returns a formatted date string, given either a Datetime instance,
     * UNIX timestamp or a valid strtotime() date string.
     *
     * @param \DateTimeInterface|string|int|null $date UNIX timestamp, strtotime() valid string
     *   or DateTime object
     * @param string|null $format Intl compatible format string.
     * @param string|bool $invalid Default value to display on invalid dates
     * @param \DateTimeZone|string|null $timezone User's timezone string or DateTimeZone object
     * @return string Formatted and translated date string
     * @throws \Exception When the date cannot be parsed
     * @see \Cake\I18n\Time::i18nFormat()
     */
    public function userFormat(
        int|string|DateTimeInterface|null $date,
        ?string $format = null,
        bool|string $invalid = false,
        string|DateTimeZone|null $timezone = null,
    ): string {
        if (empty($date)) {
            return (string)$invalid;
        }
        if (empty($timezone) && $this->getView()->getRequest()->getSession()->check('Auth.timezone')) {
            $timezone = $this->getView()->getRequest()->getSession()->read('Auth.timezone');
        }
        try {
            if (!$date instanceof DateTime) {
                if ($date instanceof DateTimeInterface) {
                    $date = new DateTime($date->format('Y-m-d H:i:s'));
                } else {
                    $date = new DateTime((string)$date);
                }
            }
            if ($timezone) {
                $date = $date->setTimezone($timezone);
            }

            return (string)$date->i18nFormat($format, $timezone);
        } catch (Exception $e) {
            if ($invalid === false) {
                throw $e;
            }

            return (string)$invalid;
        }
    }
}
