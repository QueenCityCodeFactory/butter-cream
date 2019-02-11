<?php
namespace ButterCream\View\Helper;

use BootstrapUI\View\Helper\OptionsAwareTrait;
use Cake\Utility\Inflector;
use Cake\View\Helper;
use Cake\View\StringTemplateTrait;
use Cake\View\View;

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
        ]
    ];

    /**
     * Default model of the paged sets
     *
     * @var string
     */
    protected $_defaultModel;

    /**
     * Constructor. Overridden to merge passed args with URL options.
     *
     * @param \Cake\View\View $View The View this helper is being attached to.
     * @param array $config Configuration settings for the helper.
     */
    public function __construct(View $View, array $config = [])
    {
        parent::__construct($View, $config);
        if ($this->getView()->getRequest()->getParam('paging')) {
            list($this->_defaultModel) = array_keys($this->getView()->getRequest()->getParam('paging'));
        }
    }

    /**
     * Generate Table header
     *
     * @param string $key - column key/name
     * @param string $title - alternate header title
     * @param array $options - options
     * @return string
     */
    public function header($key, $title = null, $options = [])
    {
        if (!(isset($options['keySkip']) && !empty($options['keySkip'])) && strpos($key, '.') === false) {
            $key = $this->_defaultModel . '.' . $key;
        }
        $attrs = empty($options['attrs']) ? [] : $options['attrs'];

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
            'help' => $help
        ]);
    }
}
