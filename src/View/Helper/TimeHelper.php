<?php
declare(strict_types=1);

namespace ButterCream\View\Helper;

use Cake\I18n\Time;
use Cake\View\Helper\TimeHelper as Helper;
use DateTimeInterface;
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
     * @param int|string|\DateTime $date UNIX timestamp, strtotime() valid string or DateTime object
     * @param string|null $format Intl compatible format string.
     * @param bool|string $invalid Default value to display on invalid dates
     * @param string|\DateTimeZone|null $timezone User's timezone string or DateTimeZone object
     * @return string Formatted and translated date string
     * @throws \Exception When the date cannot be parsed
     * @see \Cake\I18n\Time::i18nFormat()
     */
    public function userFormat($date, ?string $format = null, $invalid = false, $timezone = null)
    {
        if (empty($date)) {
            return $invalid;
        }
        if (empty($timezone) && $this->getView()->getRequest()->getSession()->check('Auth.timezone')) {
            $timezone = $this->getView()->getRequest()->getSession()->read('Auth.timezone');
        }
        try {
            if ($date instanceof DateTimeInterface) {
                $date = new Time($date);
            }
            $date->timezone = $timezone;

            return $date->i18nFormat($format);
        } catch (Exception $e) {
            if ($invalid === false) {
                throw $e;
            }

            return $invalid;
        }
    }
}
