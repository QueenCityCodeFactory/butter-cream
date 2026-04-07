<?php
declare(strict_types=1);

namespace ButterCream\View\Helper;

use Cake\I18n\DateTime;
use Cake\View\Helper\TimeHelper as Helper;
use Cake\View\StringTemplateTrait;
use DateTimeInterface;
use DateTimeZone;
use Exception;

/**
 * TimeHelper Class
 */
class TimeHelper extends Helper
{
    use StringTemplateTrait;

    /**
     * Default config for this class
     *
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [
        'templates' => [
            'timeTag' => '<time datetime="{{datetime}}">{{content}}</time>',
        ],
    ];

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

    /**
     * Render a relative time string ("2 hours ago", "in 3 days").
     *
     * @param \DateTimeInterface|string|int|null $date Date value.
     * @param array<string, mixed> $options Options passed to timeAgoInWords().
     * @return string
     */
    public function relativeTime(
        int|string|DateTimeInterface|null $date,
        array $options = [],
    ): string {
        if (empty($date)) {
            return '';
        }

        if (!$date instanceof DateTime) {
            if ($date instanceof DateTimeInterface) {
                $date = new DateTime($date->format('Y-m-d H:i:s'));
            } else {
                $date = new DateTime((string)$date);
            }
        }

        return $this->timeAgoInWords($date, $options);
    }

    /**
     * Render a formatted date range.
     *
     * ```
     * echo $this->Time->dateRange($start, $end); // "Jan 1 – Jan 15, 2026"
     * ```
     *
     * @param \DateTimeInterface|string|null $start Start date.
     * @param \DateTimeInterface|string|null $end End date.
     * @param string|null $format Date format (null = default i18n format).
     * @return string
     */
    public function dateRange(
        DateTimeInterface|string|null $start,
        DateTimeInterface|string|null $end,
        ?string $format = null,
    ): string {
        $startStr = $this->userFormat($start, $format);
        $endStr = $this->userFormat($end, $format);

        if (empty($startStr) && empty($endStr)) {
            return '';
        }
        if (empty($endStr)) {
            return $startStr . ' – Present';
        }
        if (empty($startStr)) {
            return 'Until ' . $endStr;
        }

        return $startStr . ' – ' . $endStr;
    }

    /**
     * Wrap a date in a semantic `<time>` element.
     *
     * @param \DateTimeInterface|string|int|null $date Date value.
     * @param string|null $format Display format.
     * @param string|bool $invalid Fallback for invalid dates.
     * @return string HTML `<time>` tag or empty string.
     */
    public function semantic(
        int|string|DateTimeInterface|null $date,
        ?string $format = null,
        bool|string $invalid = false,
    ): string {
        if (empty($date)) {
            return (string)$invalid;
        }

        $display = $this->userFormat($date, $format, $invalid);
        if ($display === (string)$invalid) {
            return $display;
        }

        if (!$date instanceof DateTimeInterface) {
            try {
                $date = new DateTime((string)$date);
            } catch (Exception) {
                return $display;
            }
        }

        $iso = $date->format('c');

        return $this->formatTemplate('timeTag', [
            'datetime' => h($iso),
            'content' => h($display),
        ]);
    }
}
