<?php
declare(strict_types=1);

namespace ButterCream\View\Helper;

use BootstrapUI\View\Helper\OptionsAwareTrait;
use Cake\View\Helper;
use Cake\View\StringTemplateTrait;

/**
 * ProgressHelper — Render Bootstrap 5 progress bars.
 *
 * ```
 * echo $this->Progress->bar(75, ['label' => '75%', 'variant' => 'success', 'striped' => true]);
 * echo $this->Progress->stacked([
 *     ['value' => 30, 'variant' => 'success'],
 *     ['value' => 20, 'variant' => 'warning'],
 *     ['value' => 10, 'variant' => 'danger'],
 * ]);
 * ```
 */
class ProgressHelper extends Helper
{
    use OptionsAwareTrait;
    use StringTemplateTrait;

    /**
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [
        'templates' => [
            'progressWrapper' => '<div{{attrs}}>{{content}}</div>',
            'progressBar' => '<div{{attrs}}>{{label}}</div>',
        ],
    ];

    /**
     * Render a single progress bar.
     *
     * ### Options
     *
     * - `variant` (string) — Bootstrap colour. Default 'primary'.
     * - `label` (string)   — Text inside the bar. Default ''.
     * - `striped` (bool)   — Striped pattern. Default false.
     * - `animated` (bool)  — Animate stripes. Default false.
     * - `min` (int)        — Minimum value. Default 0.
     * - `max` (int)        — Maximum value. Default 100.
     *
     * @param int|float $value Current progress value.
     * @param array<string, mixed> $options Bar options.
     * @return string
     */
    public function bar(int|float $value, array $options = []): string
    {
        $options += [
            'variant' => 'primary',
            'label' => '',
            'striped' => false,
            'animated' => false,
            'min' => 0,
            'max' => 100,
        ];

        $barHtml = $this->_renderBar($value, $options);

        $wrapperAttrs = $this->injectClasses(['progress'], []);
        $wrapperAttrs['role'] = 'progressbar';
        $wrapperAttrs['aria-valuenow'] = (string)(int)$value;
        $wrapperAttrs['aria-valuemin'] = (string)$options['min'];
        $wrapperAttrs['aria-valuemax'] = (string)$options['max'];
        if (!empty($options['label'])) {
            $wrapperAttrs['aria-label'] = strip_tags($options['label']);
        }

        return $this->formatTemplate('progressWrapper', [
            'attrs' => $this->templater()->formatAttributes($wrapperAttrs),
            'content' => $barHtml,
        ]);
    }

    /**
     * Render a stacked (multi-segment) progress bar.
     *
     * @param array<array<string, mixed>> $segments Array of segments, each with `value` plus bar options.
     * @param array<string, mixed> $options Wrapper HTML attributes.
     * @return string
     */
    public function stacked(array $segments, array $options = []): string
    {
        $bars = '';
        foreach ($segments as $segment) {
            $value = $segment['value'] ?? 0;
            unset($segment['value']);
            $bars .= $this->_renderBar($value, $segment + ['min' => 0, 'max' => 100]);
        }

        $options = $this->injectClasses(['progress-stacked'], $options);

        return $this->formatTemplate('progressWrapper', [
            'attrs' => $this->templater()->formatAttributes($options),
            'content' => $bars,
        ]);
    }

    /**
     * Internal — render a single progress-bar div.
     *
     * @param int|float $value Current value.
     * @param array<string, mixed> $options Bar options.
     * @return string
     */
    protected function _renderBar(int|float $value, array $options): string
    {
        $options += [
            'variant' => 'primary',
            'label' => '',
            'striped' => false,
            'animated' => false,
            'min' => 0,
            'max' => 100,
        ];

        $range = $options['max'] - $options['min'];
        $percentage = $range > 0 ? (($value - $options['min']) / $range) * 100 : 0;
        $percentage = max(0, min(100, $percentage));

        $classes = ['progress-bar'];
        if ($options['variant'] !== 'primary') {
            $classes[] = 'bg-' . $options['variant'];
        }
        if ($options['striped'] || $options['animated']) {
            $classes[] = 'progress-bar-striped';
        }
        if ($options['animated']) {
            $classes[] = 'progress-bar-animated';
        }

        $barAttrs = $this->injectClasses($classes, []);
        $barAttrs['style'] = 'width: ' . round($percentage, 1) . '%';

        return $this->formatTemplate('progressBar', [
            'attrs' => $this->templater()->formatAttributes($barAttrs),
            'label' => $options['label'] ? h($options['label']) : '',
        ]);
    }
}
