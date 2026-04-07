<?php
declare(strict_types=1);

namespace ButterCream\View\Helper;

use BootstrapUI\View\Helper\OptionsAwareTrait;
use Cake\Datasource\EntityInterface;
use Cake\Utility\Inflector;
use Cake\View\Helper;
use Cake\View\StringTemplateTrait;

use function Cake\Core\h;

/**
 * AlertHelper — Render Bootstrap 5 inline alerts and callouts.
 *
 * Distinct from FlashHelper (session-based flash messages); AlertHelper
 * renders alerts directly in templates for contextual messaging.
 *
 * ```
 * echo $this->Alert->info('This record was imported from the legacy system.');
 * echo $this->Alert->warning('Changes require admin approval.', ['dismissible' => true]);
 * echo $this->Alert->callout('danger', 'Missing required fields', $detailsHtml);
 * ```
 *
 * @property \ButterCream\View\Helper\HtmlHelper $Html
 * @extends \Cake\View\Helper<\Cake\View\View>
 */
class AlertHelper extends Helper
{
    use OptionsAwareTrait;
    use StringTemplateTrait;

    /**
     * @var array<string, array<string, string>|string>
     */
    public array $helpers = ['Html' => ['className' => 'ButterCream.Html']];

    /**
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [
        'templates' => [
            'alert' => '<div{{attrs}}>{{icon}}{{dismiss}}{{content}}</div>',
            'alertIcon' => '<div class="me-2">{{icon}}</div>',
            'alertDismiss' => '<button type="button" class="btn-close"'
                . ' data-bs-dismiss="alert" aria-label="Close"></button>',
            'alertContent' => '<div class="flex-grow-1">{{content}}</div>',
            'callout' => '<div{{attrs}}>{{title}}{{content}}</div>',
            'calloutTitle' => '<h5>{{content}}</h5>',
            'validationItem' => '<li><strong>{{field}}:</strong> {{error}}</li>',
            'validationList' => '<ul class="mb-0">{{content}}</ul>',
        ],
        'iconMap' => [
            'success' => 'check-circle',
            'danger' => 'exclamation-triangle',
            'warning' => 'exclamation-triangle',
            'info' => 'info-circle',
            'primary' => 'info-circle',
            'secondary' => 'info-circle',
        ],
    ];

    /**
     * Render a Bootstrap alert.
     *
     * @param string $variant Bootstrap colour variant (success, danger, warning, info, …).
     * @param string $message Alert message (HTML allowed).
     * @param array<string, mixed> $options Extra options:
     *   - `dismissible` (bool) — Add dismiss button. Default true.
     *   - `icon` (string|bool) — Icon name, true for auto, false for none.
     *   - `escape` (bool) — HTML-escape the message. Default false (allows HTML).
     * @return string
     */
    public function render(string $variant, string $message, array $options = []): string
    {
        $options += [
            'dismissible' => true,
            'icon' => true,
            'escape' => false,
        ];

        $classes = ['alert', 'alert-' . $variant];
        if ($options['dismissible']) {
            $classes[] = 'alert-dismissible';
            $classes[] = 'fade';
            $classes[] = 'show';
        }
        $classes[] = 'd-flex';
        $classes[] = 'align-items-center';

        // Icon
        $iconHtml = '';
        if ($options['icon'] !== false) {
            $iconName = is_string($options['icon'])
                ? $options['icon']
                : ($this->getConfig('iconMap')[$variant] ?? null);
            if ($iconName) {
                $iconHtml = $this->formatTemplate('alertIcon', [
                    'icon' => $this->Html->icon($iconName),
                ]);
            }
        }

        // Dismiss button
        $dismissHtml = '';
        if ($options['dismissible']) {
            $dismissHtml = $this->formatTemplate('alertDismiss', []);
        }

        $content = $options['escape'] ? h($message) : $message;

        unset($options['dismissible'], $options['icon'], $options['escape']);
        $options += ['role' => 'alert'];
        $options = $this->injectClasses($classes, $options);

        return $this->formatTemplate('alert', [
            'attrs' => $this->templater()->formatAttributes($options),
            'icon' => $iconHtml,
            'dismiss' => $dismissHtml,
            'content' => $this->formatTemplate('alertContent', ['content' => $content]),
        ]);
    }

    /**
     * Convenience: success alert.
     *
     * @param string $message Message content.
     * @param array<string, mixed> $options Options.
     * @return string
     */
    public function success(string $message, array $options = []): string
    {
        return $this->render('success', $message, $options);
    }

    /**
     * Convenience: danger alert.
     *
     * @param string $message Message content.
     * @param array<string, mixed> $options Options.
     * @return string
     */
    public function danger(string $message, array $options = []): string
    {
        return $this->render('danger', $message, $options);
    }

    /**
     * Convenience: warning alert.
     *
     * @param string $message Message content.
     * @param array<string, mixed> $options Options.
     * @return string
     */
    public function warning(string $message, array $options = []): string
    {
        return $this->render('warning', $message, $options);
    }

    /**
     * Convenience: info alert.
     *
     * @param string $message Message content.
     * @param array<string, mixed> $options Options.
     * @return string
     */
    public function info(string $message, array $options = []): string
    {
        return $this->render('info', $message, $options);
    }

    /**
     * Render a callout-style block (non-alert pattern used in docs/dashboards).
     *
     * @param string $variant Colour variant.
     * @param string $title Heading text.
     * @param string $content Body content (HTML allowed).
     * @param array<string, mixed> $options Extra HTML attributes.
     * @return string
     */
    public function callout(string $variant, string $title, string $content = '', array $options = []): string
    {
        $options = $this->injectClasses(['bs-callout', 'bs-callout-' . $variant], $options);

        $titleHtml = !empty($title) ? $this->formatTemplate('calloutTitle', ['content' => h($title)]) : '';

        return $this->formatTemplate('callout', [
            'attrs' => $this->templater()->formatAttributes($options),
            'title' => $titleHtml,
            'content' => $content,
        ]);
    }

    /**
     * Render entity validation errors as an alert with bulleted list.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity with errors.
     * @param array<string, mixed> $options Alert options.
     * @return string Empty string if no errors.
     */
    public function validationErrors(EntityInterface $entity, array $options = []): string
    {
        $errors = $entity->getErrors();
        if (empty($errors)) {
            return '';
        }

        $items = [];
        foreach ($errors as $field => $fieldErrors) {
            foreach ($fieldErrors as $error) {
                $items[] = $this->formatTemplate('validationItem', [
                    'field' => h(Inflector::humanize($field)),
                    'error' => h($error),
                ]);
            }
        }

        $message = $this->formatTemplate('validationList', [
            'content' => implode('', $items),
        ]);

        return $this->render('danger', $message, $options + ['icon' => 'exclamation-triangle']);
    }
}
