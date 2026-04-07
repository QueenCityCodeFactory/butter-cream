<?php
declare(strict_types=1);

namespace ButterCream\View\Helper;

use BackedEnum;
use BootstrapUI\View\Helper\OptionsAwareTrait;
use Cake\View\Helper;
use Cake\View\StringTemplateTrait;

/**
 * BadgeHelper — Render Bootstrap 5 badges, pills, and enum-mapped status indicators.
 *
 * @property \ButterCream\View\Helper\HtmlHelper $Html
 */
class BadgeHelper extends Helper
{
    use OptionsAwareTrait;
    use StringTemplateTrait;

    /**
     * @var list<string>
     */
    public array $helpers = ['Html' => ['className' => 'ButterCream.Html']];

    /**
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [
        'templates' => [
            'badge' => '<span{{attrs}}>{{icon}}{{content}}</span>',
        ],
        // Default colour for status() when no mapping matches.
        'defaultVariant' => 'secondary',
        // Map of lower-cased status strings to Bootstrap colour variants.
        'statusMap' => [
            'active' => 'success',
            'inactive' => 'secondary',
            'enabled' => 'success',
            'disabled' => 'secondary',
            'open' => 'info',
            'closed' => 'secondary',
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'draft' => 'secondary',
            'published' => 'success',
            'archived' => 'dark',
            'completed' => 'success',
            'in_progress' => 'primary',
            'in progress' => 'primary',
            'blocked' => 'danger',
            'done' => 'success',
            'cancelled' => 'secondary',
            'yes' => 'success',
            'no' => 'secondary',
            'true' => 'success',
            'false' => 'secondary',
        ],
    ];

    /**
     * Render a simple Bootstrap badge.
     *
     * ```
     * echo $this->Badge->render('New', 'primary');
     * // <span class="badge text-bg-primary">New</span>
     * ```
     *
     * @param string $text Display text.
     * @param string $variant Bootstrap colour variant (primary, success, danger, …).
     * @param array<string, mixed> $options Extra HTML attributes. Supports `icon`, `pill`, `class`.
     * @return string
     */
    public function render(string $text, string $variant = 'secondary', array $options = []): string
    {
        $options += ['pill' => false, 'icon' => null];

        $classes = ['badge', 'text-bg-' . $variant];
        if ($options['pill']) {
            $classes[] = 'rounded-pill';
        }
        unset($options['pill']);

        $iconHtml = '';
        if ($options['icon']) {
            $iconHtml = $this->Html->icon($options['icon']) . ' ';
        }
        unset($options['icon']);

        $options = $this->injectClasses($classes, $options);

        return $this->formatTemplate('badge', [
            'attrs' => $this->templater()->formatAttributes($options),
            'icon' => $iconHtml,
            'content' => h($text),
        ]);
    }

    /**
     * Render a rounded-pill badge.
     *
     * @param string $text Display text.
     * @param string $variant Bootstrap colour variant.
     * @param array<string, mixed> $options Extra HTML attributes.
     * @return string
     */
    public function pill(string $text, string $variant = 'primary', array $options = []): string
    {
        $options['pill'] = true;

        return $this->render($text, $variant, $options);
    }

    /**
     * Render a status badge using the configured status-to-colour map.
     *
     * ```
     * echo $this->Badge->status('active');
     * // <span class="badge text-bg-success">Active</span>
     * ```
     *
     * @param string|null $status Raw status value (case-insensitive lookup).
     * @param array<string, mixed> $options Supports `map` to override colour map, plus standard options.
     * @return string Returns empty string when $status is null/empty.
     */
    public function status(?string $status, array $options = []): string
    {
        if ($status === null || $status === '') {
            return '';
        }

        $map = $options['map'] ?? $this->getConfig('statusMap');
        unset($options['map']);

        $key = strtolower(str_replace(['-', ' '], '_', $status));
        $variant = $map[$key] ?? $this->getConfig('defaultVariant');

        $label = ucwords(str_replace('_', ' ', $status));

        return $this->render($label, $variant, $options);
    }

    /**
     * Render a badge from a PHP BackedEnum. Uses the enum's value for the
     * status map lookup and its name (humanised) as label.
     *
     * ```
     * echo $this->Badge->enum(ProjectStatus::Active);
     * ```
     *
     * @param \BackedEnum|null $enum Enum instance.
     * @param array<string, mixed> $options Standard badge options.
     * @return string
     */
    public function enum(?BackedEnum $enum, array $options = []): string
    {
        if ($enum === null) {
            return '';
        }

        return $this->status((string)$enum->value, $options);
    }

    /**
     * Render a boolean as a Yes/No badge.
     *
     * @param bool|int|null $value Boolean-ish value.
     * @param array<string, mixed> $options Supports `trueLabel`, `falseLabel`, `trueVariant`, `falseVariant`.
     * @return string
     */
    public function boolean(bool|int|null $value, array $options = []): string
    {
        $options += [
            'trueLabel' => 'Yes',
            'falseLabel' => 'No',
            'trueVariant' => 'success',
            'falseVariant' => 'secondary',
            'trueIcon' => 'check',
            'falseIcon' => 'xmark',
        ];

        $label = $value ? $options['trueLabel'] : $options['falseLabel'];
        $variant = $value ? $options['trueVariant'] : $options['falseVariant'];
        $icon = $value ? $options['trueIcon'] : $options['falseIcon'];

        unset(
            $options['trueLabel'],
            $options['falseLabel'],
            $options['trueVariant'],
            $options['falseVariant'],
            $options['trueIcon'],
            $options['falseIcon'],
        );

        $options['icon'] = $icon;

        return $this->render($label, $variant, $options);
    }

    /**
     * Render a numeric count badge (notification-style).
     *
     * @param int $count The count.
     * @param string $variant Bootstrap colour variant.
     * @param array<string, mixed> $options Extra HTML attributes.
     * @return string Returns empty string when count is 0.
     */
    public function count(int $count, string $variant = 'primary', array $options = []): string
    {
        if ($count === 0) {
            return '';
        }

        $options['pill'] = true;

        return $this->render((string)$count, $variant, $options);
    }

    /**
     * Render a priority badge (numeric 1-5 mapped to colours).
     *
     * ```
     * echo $this->Badge->priority(1); // <span class="badge text-bg-danger">Critical</span>
     * ```
     *
     * @param int|null $level Priority level (1=highest/critical … 5=lowest).
     * @param array<string, mixed> $options Supports `labels` and `variants` overrides.
     * @return string
     */
    public function priority(?int $level, array $options = []): string
    {
        if ($level === null) {
            return '';
        }

        $labels = $options['labels'] ?? [
            1 => 'Critical',
            2 => 'High',
            3 => 'Medium',
            4 => 'Low',
            5 => 'Lowest',
        ];
        $variants = $options['variants'] ?? [
            1 => 'danger',
            2 => 'warning',
            3 => 'info',
            4 => 'secondary',
            5 => 'light',
        ];
        unset($options['labels'], $options['variants']);

        $label = $labels[$level] ?? "P{$level}";
        $variant = $variants[$level] ?? $this->getConfig('defaultVariant');

        return $this->render($label, $variant, $options);
    }
}
