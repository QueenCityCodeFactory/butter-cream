<?php
declare(strict_types=1);

namespace ButterCream\View\Helper;

use BootstrapUI\View\Helper\OptionsAwareTrait;
use Cake\Utility\Inflector;
use Cake\View\Helper;
use Cake\View\StringTemplateTrait;

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
     * @var list<string>
     */
    public array $helpers = ['Html', 'Paginator'];

    /**
     * Default config for this class
     *
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [
        'templates' => [
            'tableheader' => '<th{{attrs}}>{{content}}{{help}}</th>',
        ],
    ];

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
}
