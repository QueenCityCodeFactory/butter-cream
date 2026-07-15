<?php
declare(strict_types=1);

namespace ButterCream\View\Helper;

use ArrayAccess;
use Cake\I18n\DateTime;
use Cake\View\Helper\TimeHelper as Helper;
use Cake\View\StringTemplateTrait;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use function Cake\Core\h;

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
        'outputTimezone' => null,
        'timezoneAttribute' => 'timezone',
        'identityAttribute' => 'identity',
        'identityTimezoneField' => 'timezone',
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
        if ($date === null || $date === '') {
            return (string)$invalid;
        }

        try {
            $timezone = $this->resolveUserTimezone($timezone);
            $date = $this->normalizeDateTime($date);
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
        if ($date === null || $date === '') {
            return '';
        }

        $date = $this->normalizeDateTime($date);

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
        if ($date === null || $date === '') {
            return (string)$invalid;
        }

        $display = $this->userFormat($date, $format, $invalid);
        if ($display === (string)$invalid) {
            return $display;
        }

        try {
            $date = $this->normalizeDateTime($date);
        } catch (Exception) {
            return $display;
        }

        $iso = $date->format('c');

        return $this->formatTemplate('timeTag', [
            'datetime' => h($iso),
            'content' => h($display),
        ]);
    }

    /**
     * Resolve the output timezone for a user-facing date.
     *
     * Resolution order is the explicit argument, helper outputTimezone config,
     * request timezone attribute, authenticated identity field, then the legacy
     * Auth.timezone session value.
     *
     * @param \DateTimeZone|string|null $timezone Explicit timezone override.
     * @return \DateTimeZone|string|null
     */
    protected function resolveUserTimezone(
        string|DateTimeZone|null $timezone = null,
    ): string|DateTimeZone|null {
        if ($timezone instanceof DateTimeZone || (is_string($timezone) && $timezone !== '')) {
            return $timezone;
        }

        $timezone = $this->validTimezone($this->getConfig('outputTimezone'));
        if ($timezone !== null) {
            return $timezone;
        }

        $request = $this->getView()->getRequest();
        $timezoneAttribute = $this->getConfig('timezoneAttribute');
        if (is_string($timezoneAttribute) && $timezoneAttribute !== '') {
            $timezone = $this->validTimezone($request->getAttribute($timezoneAttribute));
            if ($timezone !== null) {
                return $timezone;
            }
        }

        $identityAttribute = $this->getConfig('identityAttribute');
        $identityTimezoneField = $this->getConfig('identityTimezoneField');
        if (is_string($identityAttribute) && is_string($identityTimezoneField)) {
            $identity = $request->getAttribute($identityAttribute);
            $timezone = $this->validTimezone($this->readIdentityField($identity, $identityTimezoneField));
            if ($timezone !== null) {
                return $timezone;
            }
        }

        $session = $request->getSession();
        if ($session->check('Auth.timezone')) {
            return $this->validTimezone($session->read('Auth.timezone'));
        }

        return null;
    }

    /**
     * Normalize a supported date value without changing its instant.
     *
     * @param \DateTimeInterface|string|int $date Date value.
     * @return \Cake\I18n\DateTime
     */
    protected function normalizeDateTime(
        int|string|DateTimeInterface $date,
    ): DateTime {
        if ($date instanceof DateTime) {
            return $date;
        }
        if ($date instanceof DateTimeInterface) {
            return DateTime::createFromInterface($date);
        }
        if (is_int($date)) {
            return DateTime::createFromTimestamp($date);
        }

        return new DateTime($date);
    }

    /**
     * Return a valid timezone candidate or null.
     *
     * @param mixed $timezone Timezone candidate.
     * @return \DateTimeZone|string|null
     */
    protected function validTimezone(mixed $timezone): string|DateTimeZone|null
    {
        if ($timezone instanceof DateTimeZone) {
            return $timezone;
        }
        if (!is_string($timezone) || trim($timezone) === '') {
            return null;
        }

        $timezone = trim($timezone);
        try {
            new DateTimeZone($timezone);
        } catch (Exception) {
            return null;
        }

        return $timezone;
    }

    /**
     * Read a timezone field from a framework-neutral identity value.
     *
     * @param mixed $identity Request identity.
     * @param string $field Identity timezone field.
     * @return mixed
     */
    protected function readIdentityField(mixed $identity, string $field): mixed
    {
        if ($field === '') {
            return null;
        }
        if (is_array($identity)) {
            return $identity[$field] ?? null;
        }
        if ($identity instanceof ArrayAccess && isset($identity[$field])) {
            return $identity[$field];
        }
        if (is_object($identity) && isset($identity->{$field})) {
            return $identity->{$field};
        }

        return null;
    }
}
