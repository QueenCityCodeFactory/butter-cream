<?php
declare(strict_types=1);

namespace ButterCream\View\Helper;

use BootstrapUI\View\Helper\OptionsAwareTrait;
use Cake\Utility\Inflector;
use Cake\View\Helper;
use Cake\View\StringTemplateTrait;
use function Cake\Core\h;

/**
 * Table Helper
 *
 * @property \ButterCream\View\Helper\HtmlHelper $Html
 * @property \ButterCream\View\Helper\PaginatorHelper $Paginator
 * @extends \Cake\View\Helper<\Cake\View\View>
 */
class TableHelper extends Helper
{
    use OptionsAwareTrait;
    use StringTemplateTrait;

    /**
     * List of helpers used by this helper
     *
     * @var array<string, array<string, string>|string>
     */
    public array $helpers = [
        'Html' => ['className' => 'ButterCream.Html'],
        'Paginator' => ['className' => 'ButterCream.Paginator'],
        'Form' => ['className' => 'ButterCream.Form'],
    ];

    /**
     * Default config for this class
     *
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [
        'templates' => [
            'tableheader' => '<th{{attrs}}>{{content}}{{help}}</th>',
            'checkboxAll' => '<input type="checkbox" class="form-check-input" data-check-all>',
            'rowCheckbox' => '<td class="text-center"><input type="checkbox"'
                . ' class="form-check-input bulk-check" name="{{name}}" value="{{value}}"></td>',
            'actionsCell' => '<td class="actions">{{content}}</td>',
            'emptyState' => '<tr><td colspan="{{colspan}}"'
                . ' class="text-center text-muted py-5">{{icon}}{{message}}{{action}}</td></tr>',
            'emptyStateIcon' => '<em class="fa-solid fa-{{icon}} fa-3x mb-3 d-block text-muted"></em>',
            'emptyStateAction' => '<div class="mt-2">{{content}}</div>',
            'sortHeader' => '<th{{attrs}}></th>',
            'sortHandle' => '<td{{attrs}}><i class="fa-solid fa-grip-vertical text-muted"></i></td>',
        ],
    ];

    /**
     * Render a `<th>` column header for a drag-and-drop sort handle.
     *
     * ```
     * <?= $this->Table->sortHeader() ?>
     * ```
     *
     * @param array<string, mixed> $options HTML attributes for the `<th>`.
     * @return string
     */
    public function sortHeader(array $options = []): string
    {
        $options += [
            'scope' => 'col',
            'class' => 'text-center',
            'style' => 'width:2rem;',
            'aria-label' => 'Drag to Reorder',
        ];

        return $this->formatTemplate('sortHeader', [
            'attrs' => $this->templater()->formatAttributes($options),
        ]);
    }

    /**
     * Render a `<td>` drag handle cell for a sortable table row.
     *
     * Attach `draggable="true"` and the corresponding drag events to the row itself.
     * This cell only provides the visual grip icon and the `.drag-handle` hook.
     *
     * ```
     * <?= $this->Table->sortHandle() ?>
     * ```
     *
     * @param array<string, mixed> $options HTML attributes for the `<td>`.
     * @return string
     */
    public function sortHandle(array $options = []): string
    {
        $options += [
            'class' => 'text-center drag-handle',
            'title' => 'Drag to reorder',
            'style' => 'cursor:grab;',
        ];

        return $this->formatTemplate('sortHandle', [
            'attrs' => $this->templater()->formatAttributes($options),
        ]);
    }

    /**
     * Generate Table header
     *
     * @param string $key - column key/name
     * @param string|null $title - alternate header title
     * @param array<string, mixed> $options - options
     * @return string
     */
    public function header(string $key, ?string $title = null, array $options = []): string
    {
        $attrs = $options['attrs'] ?? [];
        unset($options['attrs']);

        $help = '';
        if (!empty($options['help'])) {
            $help = ' ' . $options['help'];
        }
        unset($options['help']);

        if (empty($attrs['class'])) {
            $attrs['class'] = [];
        }

        if (!is_array($attrs['class'])) {
            $attrs['class'] = [$attrs['class']];
        }

        if (empty($title)) {
            // As Model names are now required for all fields,
            // remove the base model from the title
            $keyArr = explode('.', $key);
            $title = array_pop($keyArr);

            if (str_contains($title, '.')) {
                $title = str_replace('.', ' ', $title);
            }

            $title = __(Inflector::humanize((string)preg_replace('/_id$/', '', $title)));
        }

        $sort = isset($options['sort']) && $options['sort'] === false ? false : true;
        unset($options['sort']);

        if (isset($options['sortWhiteList'])) {
            $sort = in_array($key, $options['sortWhiteList']);
            unset($options['sortWhiteList']);
        }

        if ($sort === true) {
            $content = $this->Paginator->sort($key, $title, $options);
            $attrs['class'][] = 'col-sort';
        } else {
            $content = $title;
        }
        $attrs += [
            'data-key' => $key,
            'data-title' => $title,
        ];

        $attrs = $this->injectClasses($attrs['class'], $attrs);

        return $this->formatTemplate('tableheader', [
            'attrs' => $this->templater()->formatAttributes($attrs),
            'content' => $content,
            'help' => $help,
        ]);
    }

    /**
     * Render a select-all checkbox for the table header.
     *
     * Works with JavaScript to toggle all row checkboxes with `data-check-all`.
     *
     * @param array<string, mixed> $options HTML attributes for the `<th>`.
     * @return string
     */
    public function headerCheckbox(array $options = []): string
    {
        $attrs = $options['attrs'] ?? [];
        unset($options['attrs']);

        $attrs = $this->injectClasses(['text-center'], $attrs);

        $checkbox = $this->formatTemplate('checkboxAll', []);

        return $this->formatTemplate('tableheader', [
            'attrs' => $this->templater()->formatAttributes($attrs),
            'content' => $checkbox,
            'help' => '',
        ]);
    }

    /**
     * Render a row checkbox for bulk selection.
     *
     * @param string|int $id Row identifier (entity primary key).
     * @param array<string, mixed> $options HTML attributes for the checkbox.
     * @return string `<td>` element with checkbox.
     */
    public function rowCheckbox(string|int $id, array $options = []): string
    {
        $options += ['name' => 'bulk_ids[]'];

        return $this->formatTemplate('rowCheckbox', [
            'name' => h($options['name']),
            'value' => h((string)$id),
        ]);
    }

    /**
     * Render standard action buttons (view, edit, delete) for a table row.
     *
     * ```
     * echo $this->Table->actions($entity->id);
     * echo $this->Table->actions($entity->id, ['view', 'edit']); // subset
     * ```
     *
     * @param string|int $primaryKey Entity primary key.
     * @param list<string> $buttons Button types to include. Default: ['view', 'edit', 'delete'].
     * @param array<string, mixed> $options Additional options passed to each button method.
     * @return string `<td>` with action buttons.
     */
    public function actions(
        string|int $primaryKey,
        array $buttons = ['view', 'edit', 'delete'],
        array $options = [],
    ): string {
        $html = '';
        foreach ($buttons as $btn) {
            $html .= match ($btn) {
                'view' => $this->Html->viewBtn($primaryKey, $options),
                'edit' => $this->Html->editBtn($primaryKey, $options),
                /** @psalm-suppress UndefinedMethod */
                'delete' => $this->_View->loadHelper('Form', ['className' => 'ButterCream.Form'])
                    ->deleteBtn($primaryKey, $options), // @phpstan-ignore method.notFound
                default => '',
            };
            $html .= "\n";
        }

        return $this->formatTemplate('actionsCell', [
            'content' => trim($html),
        ]);
    }

    /**
     * Render an empty-state row when a table has no data.
     *
     * ```
     * <?php if ($entities->isEmpty()) : ?>
     *     <?= $this->Table->emptyState('No users found.', ['colspan' => 8]) ?>
     * <?php endif; ?>
     * ```
     *
     * @param string $message Message to display.
     * @param array<string, mixed> $options Supports `colspan` (int), `icon` (string), `action` (string HTML).
     * @return string A `<tr><td>` element.
     */
    public function emptyState(string $message, array $options = []): string
    {
        $options += [
            'colspan' => 1,
            'icon' => 'inbox',
            'action' => null,
        ];

        $iconHtml = '';
        if ($options['icon']) {
            $iconHtml = $this->formatTemplate('emptyStateIcon', [
                'icon' => h($options['icon']),
            ]);
        }

        $actionHtml = '';
        if ($options['action']) {
            $actionHtml = $this->formatTemplate('emptyStateAction', [
                'content' => $options['action'],
            ]);
        }

        return $this->formatTemplate('emptyState', [
            'colspan' => (string)(int)$options['colspan'],
            'icon' => $iconHtml,
            'message' => h($message),
            'action' => $actionHtml,
        ]);
    }
}
