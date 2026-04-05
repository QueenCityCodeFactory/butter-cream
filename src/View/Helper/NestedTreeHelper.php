<?php
declare(strict_types=1);

namespace ButterCream\View\Helper;

use Cake\View\Helper;
use Cake\View\StringTemplateTrait;

/**
 * Nested Tree Helper
 */
class NestedTreeHelper extends Helper
{
    use StringTemplateTrait;

    /**
     * List of helpers used by this helper
     *
     * @var array
     */
    public array $helpers = ['Html'];

    /**
     * Default config for this class
     *
     * @var array
     */
    protected array $_defaultConfig = [
        'templates' => [
            'container' => '<div{{attrs}}>{{content}}</div>',
            'list' => '<ul{{attrs}}>{{content}}</ul>',
            'listItem' => '<li{{attrs}}><div class="list-group-item">{{title}}</div>{{content}}</li>',
        ],
    ];

    /**
     * Generate HTML for Nested Tree Sorting
     *     ### Options:
     *     - `container`
     *     - `nestingKey`
     *     - `items`
     *     - `sortable`
     *
     * @param array $tree The tree data - needs to be tree structure
     * @param array $options The options
     * @return string|bool The HTML for the sorter
     */
    public function sorter(array $tree = [], array $options = []): bool|string
    {
        if (empty($tree)) {
            return false;
        }

        $container = $options['container'] ?? [];
        $sortable = $options['sortable'] ?? [];
        $items = $options['items'] ?? [];
        $nestingKey = $options['nestingKey'] ?? 'children';

        if (!isset($container['class'])) {
            $container['class'] = 'nested-sortable';
        }
        if (!isset($sortable['class'])) {
            $sortable['class'] = 'sortable list-group';
        }

        return $this->formatTemplate('container', [
            'attrs' => $this->templater()->formatAttributes($container),
            'content' => $this->buildList($tree, [
                'root' => $sortable,
                'items' => $items,
                'nestingKey' => $nestingKey,
            ]),
        ]);
    }

    /**
     * Recursive helper function for sorter method
     *     ### Options:
     *     - `root`
     *     - `nestingKey`
     *     - `items`
     *     - `data-id`
     *
     * @param array $tree The tree data - needs to be tree structure
     * @param array $options The Options
     * @return string HTML for the sorter
     */
    protected function buildList(array $tree = [], array $options = []): string
    {
        if (empty($tree)) {
            return '';
        }
        $root = [];
        if (!empty($options['root'])) {
            $root = $options['root'];
        }
        unset($options['root']);

        $nestingKey = $options['nestingKey'] ?? 'children';
        unset($options['nestingKey']);

        if (!empty($options['items'])) {
            $itemsOptions = $options['items'];
            $options = $itemsOptions;
        }
        $listItems = [];
        foreach ($tree as $treeItem) {
            $options['data-id'] = $treeItem->id;
            $listItems[] = $this->formatTemplate('listItem', [
                'attrs' => $this->templater()->formatAttributes($options),
                'title' => h($treeItem->name),
                'content' => $this->buildList(
                    !empty($treeItem->$nestingKey) ? $treeItem->$nestingKey : [],
                    $options + ['nestingKey' => $nestingKey],
                ),
            ]);
        }

        return $this->formatTemplate('list', [
            'attrs' => $this->templater()->formatAttributes($root),
            'content' => implode('', $listItems),
        ]);
    }
}
