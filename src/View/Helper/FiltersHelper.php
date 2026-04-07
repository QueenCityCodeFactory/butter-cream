<?php
declare(strict_types=1);

namespace ButterCream\View\Helper;

use Cake\View\Helper;

/**
 * Filters Helper
 *
 * Renders a Bootstrap 5 dropdown-based filter drawer for index pages.
 * Register filter controls in the template; the layout calls render()
 * to output the toggle button and drawer panel.
 *
 * Works in both standalone page and AJAX (relatedData) contexts.
 *
 * @property \ButterCream\View\Helper\FormHelper $Form
 * @property \ButterCream\View\Helper\HtmlHelper $Html
 * @extends \Cake\View\Helper<\Cake\View\View>
 */
class FiltersHelper extends Helper
{
    /**
     * @var array<string, array<string, string>|string>
     */
    public array $helpers = [
        'Form' => ['className' => 'ButterCream.Form'],
        'Html' => ['className' => 'ButterCream.Html'],
    ];

    /**
     * Setup options for the current drawer.
     *
     * @var array<string, mixed>
     */
    protected array $_setup = [];

    /**
     * Buffered filter controls (field => options).
     *
     * @var array<string, array<string, mixed>>
     */
    protected array $_controls = [];

    /**
     * Configure the filters drawer.
     *
     * ### Options
     *
     * - `id`     DOM ID for the drawer (default: auto-generated from controller name)
     * - `title`  Drawer heading text (default: 'Filters')
     * - `update` CSS selector for AJAX update target (default: reads ajax_update_element)
     * - `url`    Base URL for the filter form action (default: current request path)
     *
     * @param array<string, mixed> $options Drawer configuration.
     * @return void
     */
    public function setup(array $options = []): void
    {
        $view = $this->getView();
        $request = $view->getRequest();

        $options += [
            'id' => strtolower((string)$request->getParam('controller')) . '-filters',
            'title' => __('Filters'),
            'update' => $view->fetch('ajax_update_element'),
            'url' => $request->getUri()->getPath(),
        ];

        $this->_setup = $options;
    }

    /**
     * Register a filter control.
     *
     * Options are passed directly to FormHelper::control(). Field values
     * are automatically populated from the current query parameters.
     *
     * @param string $field The query parameter / form field name.
     * @param array<string, mixed> $options FormHelper::control() options.
     * @return void
     */
    public function addControl(string $field, array $options = []): void
    {
        if (empty($this->_setup)) {
            $this->setup();
        }

        $this->_controls[$field] = $options;
    }

    /**
     * Whether any filter controls have been registered.
     *
     * @return bool
     */
    public function hasFilters(): bool
    {
        return !empty($this->_controls);
    }

    /**
     * Render the complete filters dropdown (toggle button + drawer panel).
     *
     * @param array<string, mixed> $options Additional options for the toggle button.
     * @return string HTML string or empty if no filters registered.
     */
    public function render(array $options = []): string
    {
        if (!$this->hasFilters()) {
            return '';
        }

        $setup = $this->_setup;
        $query = $this->getView()->getRequest()->getQueryParams();
        $activeCount = $this->_countActive();

        // --- Toggle Button ---
        $badgeHtml = $activeCount > 0
            ? ' <span class="badge bg-primary rounded-pill">' . $activeCount . '</span>'
            : '';

        $html = '<div class="filters-wrapper dropdown">';
        $html .= '<button class="btn btn-outline-secondary btn-xs" type="button"';
        $html .= ' data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">';
        $html .= $this->Html->icon('filter');
        $html .= $badgeHtml;
        $html .= '</button>';

        // --- Dropdown Panel ---
        $html .= '<div class="dropdown-menu dropdown-menu-end filters-drawer" id="' . h($setup['id']) . '">';
        $html .= '<div class="filters-drawer__header">';
        $html .= '<h6 class="mb-0">' . h($setup['title']) . '</h6>';
        $html .= '</div>';
        $html .= '<div class="filters-drawer__body">';

        // Form (GET, no referer, values from query params)
        $formOptions = [
            'type' => 'get',
            'noreferer' => true,
            'valueSources' => ['query'],
            'url' => $setup['url'],
            'class' => 'filters-form ajax-search-form',
        ];
        if (!empty($setup['update'])) {
            $formOptions['data-update'] = $setup['update'];
            $formOptions['data-url'] = $setup['url'];
        }

        $html .= $this->Form->create(null, $formOptions);

        // Hidden fields for non-filter query params (preserves scope + sort + limit)
        $filterFields = array_keys($this->_controls);
        foreach ($query as $key => $value) {
            if (
                !in_array($key, $filterFields, true)
                && $key !== 'page'
                && is_scalar($value)
            ) {
                $html .= $this->Form->hidden($key, ['value' => (string)$value, 'data-keep-value' => '1']);
            }
        }

        // Filter controls
        foreach ($this->_controls as $field => $fieldOptions) {
            $html .= $this->Form->control($field, $fieldOptions);
        }

        // Action buttons
        $html .= '<div class="filters-actions d-flex gap-2">';
        $html .= '<button type="submit" class="btn btn-primary btn-sm flex-fill">';
        $html .= $this->Html->icon('check') . ' ' . __('Apply');
        $html .= '</button>';
        $html .= '<button type="button" class="btn btn-outline-secondary btn-sm flex-fill clear-search-btn">';
        $html .= $this->Html->icon('xmark') . ' ' . __('Clear');
        $html .= '</button>';
        $html .= '</div>';

        $html .= $this->Form->end();
        $html .= '</div>'; // filters-drawer__body
        $html .= '</div>'; // dropdown-menu
        $html .= '</div>'; // filters-wrapper

        // Reset state for potential next render (multiple index sections on one page)
        $this->_controls = [];
        $this->_setup = [];

        return $html;
    }

    /**
     * Count how many filter fields have active (non-empty) values.
     *
     * @return int
     */
    protected function _countActive(): int
    {
        $query = $this->getView()->getRequest()->getQueryParams();
        $count = 0;

        foreach ($this->_controls as $field => $options) {
            if (isset($query[$field]) && $query[$field] !== '') {
                $count++;
            }
        }

        return $count;
    }
}
