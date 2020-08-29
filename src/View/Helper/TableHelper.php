<?php
declare(strict_types=1);

namespace ButterCream\View\Helper;

use BootstrapUI\View\Helper\OptionsAwareTrait;
use Cake\Utility\Inflector;
use Cake\View\Helper;
use Cake\View\StringTemplateTrait;

/**
 * Table Helper
 */
class TableHelper extends Helper
{
    use OptionsAwareTrait;
    use StringTemplateTrait;

    /**
     * List of helpers used by this helper
     *
     * @var array
     */
    public $helpers = ['Html', 'Paginator'];

    /**
     * Default config for this class
     *
     * @var array
     */
    protected $_defaultConfig = [
        'templates' => [
            'tableheader' => '<th{{attrs}}>{{content}}{{help}}</th>',
        ],
    ];

    /**
     * Generate Table header
     *
     * @param string $key - column key/name
     * @param string|null $title - alternate header title
     * @param array $options - options
     * @return string
     */
    public function header(string $key, $title = null, array $options = []): string
    {
        $options += [
            'modelClass' => $this->getView()->getRequest()->getParam('controller'),
        ];

        if (isset($options['modelClass'])) {
            $key = $options['modelClass'] . '.' . $key;
        }
        unset($options['modelClass']);

        $attrs = $options['attrs'] ?? [];

        $help = '';
        if (!empty($options['help'])) {
            $help = ' ' . $options['help'];
        }

        if (empty($attrs['class'])) {
            $attrs['class'] = [];
        }

        if (!is_array($attrs['class'])) {
            $attrs['class'] = [$attrs['class']];
        }

        if (empty($title)) {
            $keyArr = explode('.', $key); // As Model names are now required for all fields, remove the base model from the title
            $title = array_pop($keyArr);

            if (strpos($title, '.') !== false) {
                $title = str_replace('.', ' ', $title);
            }

            $title = __(Inflector::humanize(preg_replace('/_id$/', '', $title)));
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
