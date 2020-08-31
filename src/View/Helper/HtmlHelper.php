<?php
declare(strict_types=1);

namespace ButterCream\View\Helper;

use BootstrapUI\View\Helper\HtmlHelper as Helper;
use BootstrapUI\View\Helper\OptionsAwareTrait;
use Cake\View\View;

/**
 * Html Helper
 */
class HtmlHelper extends Helper
{
    use OptionsAwareTrait;

    /**
     * Default config for this class
     *
     * @var array
     */
    protected $_templates = [
        'templates' => [
            'actionDropdown' => '<a class="btn btn-outline-secondary btn-sq-xs dropdown-toggle action-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{content}}</a>',
            'tag' => '<{{tag}}{{attrs}}>{{content}}</{{tag}}>',
        ],
    ];

    /**
     * Constructor
     *
     * ### Settings
     *
     * - `useGlyphicons` Bool True to use Glyphicons OR False to use FontAwesome icons (default: false)
     * - `templates` Either a filename to a config containing templates.
     *   Or an array of templates to load. See Cake\View\StringTemplate for
     *   template formatting.
     *
     * ### Customizing tag sets
     *
     * Using the `templates` option you can redefine the tag HtmlHelper will use.
     *
     * @param \Cake\View\View $View The View this helper is being attached to.
     * @param array $config Configuration settings for the helper.
     */
    public function __construct(View $View, array $config = [])
    {
        $this->_defaultConfig['templates'] = $this->_templates['templates'] += $this->_defaultConfig['templates'];
        parent::__construct($View, $config);
    }

    /**
     * Returns bootstrap icon markup. By default, uses `<i>` tag and font awesome icon set.
     *
     * @param string $name Name of icon (i.e. search, leaf, etc.).
     * @param array $options Additional options and HTML attributes.
     * ### Options
     *
     * - `iconSet`: Common class name for the icon set. Default 'fas'.
     * - `prefix`: Prefix for class names. Default 'fa'.
     * - `size`: Size class will be generated based of this. For e.g. if you use
     *   size 'lg' class '<prefix>-lg` will be added. Default null.
     *
     * You can use `iconDefaults` option for the helper to set default values
     * for above options.
     * @return string HTML icon markup.
     */
    public function icon(string $name, array $options = []): string
    {
        $options += [
            'tag' => 'em',
        ];

        return parent::icon($name, $options);
    }

    /**
     * Tooltip - Useful for Help messages
     *
     * @param string $text The text
     * @param array $options The options
     * @return string HTML icon with Tooltip markup
     */
    public function tooltip(string $text, array $options = []): string
    {
        $options += [
            'icon' => 'info-circle',
            'class' => 'text-primary',
            'title' => $text,
            'data-toggle' => 'tooltip',
            'data-placement' => 'auto',
        ];

        $icon = $options['icon'];
        unset($options['icon']);

        return $this->icon($icon, $options);
    }

    /**
     * Popover - Useful for Help messages
     *
     * @param string $title The title
     * @param string $text The text
     * @param array $options The options
     * @return string HTML icon with Popover markup
     */
    public function popover(string $title, string $text, array $options = []): string
    {
        $options += [
            'icon' => 'question-circle',
            'class' => 'text-primary',
            'title' => $title,
            'data-toggle' => 'popover',
            'data-content' => $text,
            'data-placement' => 'top',
        ];

        $icon = $options['icon'];
        unset($options['icon']);

        return $this->icon($icon, $options);
    }

    /**
     * Creates an HTML link.
     *
     * If $url starts with "http://" this is treated as an external link. Else,
     * it is treated as a path to controller/action and parsed with the
     * UrlHelper::url() method.
     *
     * If the $url is empty, $title is used instead.
     *
     * ### Options
     *
     * - `escape` Set to false to disable escaping of title and attributes.
     * - `escapeTitle` Set to false to disable escaping of title. Takes precedence
     *   over value of `escape`)
     * - `confirm` Bootstap Confirmation confirmation message.
     * - `tooltip` - adds Bootstrap popover tooltip to link, set false to remove tooltip
     * - `title` - set title attribute activates tooltip
     *
     * @param string $title The content to be wrapped by <a> tags.
     * @param string|array|null $url Cake-relative URL or array of URL parameters, or
     *   external URL (starts with http://)
     * @param array $options Array of options and HTML attributes.
     * @return string An `<a />` element.
     * @link http://book.cakephp.org/3.0/en/views/helpers/html.html#creating-links
     */
    public function link($title, $url = null, array $options = []): string
    {
        $options += ['tooltip' => true];

        if (isset($options['title']) && $options['tooltip'] === true) {
            $options += [
                'data-toggle' => 'tooltip',
                'data-placement' => 'auto',
            ];
        }
        unset($options['tooltip']);

        if (isset($options['confirm'])) {
            if (isset($options['class'])) {
                $options['class'] .= ' modal-confirm';
            } else {
                $options['class'] = 'modal-confirm';
            }

            $options += [
                'data-modal' => 1,
                'data-modal-link' => 1,
                'data-modal-message' => $options['confirm'],
            ];
        }
        unset($options['confirm']);

        return parent::link($title, $url, $options);
    }

    /**
     * Creates an HTML link wrapped in a tag
     *
     * If $url starts with "http://" this is treated as an external link. Else,
     * it is treated as a path to controller/action and parsed with the
     * UrlHelper::url() method.
     *
     * If the $url is empty, $title is used instead.
     *
     * ### Options
     *
     * - `escape` Set to false to disable escaping of title and attributes.
     * - `escapeTitle` Set to false to disable escaping of title. Takes precedence
     *   over value of `escape`)
     * - `confirm` JavaScript confirmation message.
     *
     * @param string $title The content to be wrapped by <a> tags.
     * @param string|array|null $url Cake-relative URL or array of URL parameters, or
     *   external URL (starts with http://)
     * @param array $options Array of options and HTML attributes.
     * @return string An `<a />` element.
     * @link http://book.cakephp.org/3.0/en/views/helpers/html.html#creating-links
     */
    public function tagLink($title, $url = null, array $options = []): string
    {
        $options += ['tag' => ['name' => 'li', 'options' => []]];

        if (is_array($options['tag'])) {
            $name = $options['tag']['name'];
            $tagOptions = $options['tag']['options'];
        } else {
            $name = $options['tag'];
            $tagOptions = [];
        }

        unset($options['tag']);

        return $this->formatTemplate('tag', [
            'tag' => $name,
            'attrs' => $this->templater()->formatAttributes($tagOptions),
            'content' => $this->link($title, $url, $options),
        ]);
    }

    /**
     * Returns Bootstrap `button` markup. By default, uses `<button>`.
     * Optionally the button can be an `<a>` tag with a linking URL.
     *
     * @param string $title Text/HTML to be put on the button.
     * @param array $options Additional HTML attributes.
     * Options Include:
     *  - `url` The url to link to if this is an `A` tag
     *  - `tag` The type of tag to use for the button (BUTTON|A)
     *  - `type` The emphasis class to be placed on the button
     *  - `size` The size of button to produce
     *  - `tooltip` Whether or not to place a tooltip on the button. Optionally can contain the text to be used as the tooltip.
     * @return string HTML button markup using either `<button>` OR `<A>`
     */
    public function button(string $title, array $options = []): string
    {
        $options += [
            'url' => [],
            'tag' => 'button',
            'type' => 'primary',
            'size' => null,
            'icon' => null,
            'text' => '',
            'tooltip' => false,
            'disabled' => false,
            'class' => [],
        ];

        $tag = $options['tag'];
        unset($options['tag']);

        if ($options['tooltip']) {
            if (is_string($options['tooltip'])) {
                $options['title'] = $options['tooltip'];
            }
            if (!empty($options['title'])) {
                $options += [
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'auto',
                ];
            }
        }
        unset($options['tooltip']);

        $classes = [];
        if (empty($options['class'])) {
            $classes = ['btn'];
            if (!empty($options['type'])) {
                $classes[] = 'btn-' . $options['type'];
            }
            if (!empty($options['size'])) {
                $classes[] = 'btn-' . $options['size'];
            }
        } elseif (is_array($options['class'])) {
            $classes = $options['class'];
        } elseif (is_string($options['class'])) {
            $classes = [$options['class']];
        }
        unset($options['class']);
        unset($options['type']);
        unset($options['size']);

        $url = $options['url'];
        unset($options['url']);

        if ($options['disabled']) {
            $tag = 'button';
            $url = null;
        }

        if (empty($url) && $tag === 'button') {
            return $this->tag($tag, $title, $this->injectClasses($classes, $options));
        }

        return $this->link($title, $url, $this->injectClasses($classes, $options += ['escape' => false]));
    }

    /**
     * View Button for Index Pages
     *
     * @param string|int $primaryKey The primary key
     * @param array $options The standard options for links
     * @return string HTML Link
     */
    public function viewBtn($primaryKey, array $options = []): string
    {
        $options += [
            'escape' => false,
            'title' => 'View/Info',
            'class' => 'btn btn-outline-primary btn-xs btn-sq-xs',
        ];

        return $this->link($this->icon('info'), ['action' => 'view', $primaryKey], $options);
    }

    /**
     * Edit Button for Index Pages
     *
     * @param string|int $primaryKey The primary key
     * @param array $options The standard options for links
     * @return string HTML Link
     */
    public function editBtn($primaryKey, array $options = []): string
    {
        $options += [
            'escape' => false,
            'title' => 'Edit',
            'class' => 'btn btn-outline-light-orange btn-xs btn-sq-xs',
        ];

        return $this->link($this->icon('edit'), ['action' => 'edit', $primaryKey], $options);
    }

    /**
     * Add Button for Cards
     *
     * @param array $options The options
     * @return string|bool Html Link
     */
    public function addBtn(array $options = [])
    {
        $url = ['action' => 'add'];
        $options['default'] = false;
        $options['escape'] = false;
        if (!empty($options['url'])) {
            $url = $options['url'];
        }
        unset($options['url']);
        if (empty($options['class'])) {
            $options['class'] = 'btn btn-success btn-sq-xs';
        }

        return $this->link($this->icon('plus'), $url, $options);
    }

    /**
     * Creates and action dropdown menu
     *
     * @param array $tagLinks An array of tagLinks, example: <li><a>Some Link in a List Item</a></li>
     * @param string $menuButton The Menu button to use
     * @return string|bool Menu HTML otherwise false
     */
    public function actionDropdownMenu(array $tagLinks = [], string $menuButton = '<em class="fa fa-bars"></em>')
    {
        $menuItems = [];
        foreach ($tagLinks as $tagLink) {
            if (!empty($tagLink)) {
                $menuItems[] = $tagLink . "\n";
            }
        }
        if (!empty($menuItems)) {
            $button = $this->formatTemplate('actionDropdown', [
                'content' => $menuButton,
            ]) . "\n";

            return $button . $this->formatTemplate('tag', [
                'tag' => 'div',
                'attrs' => $this->templater()->formatAttributes([
                    'class' => 'dropdown-menu dropdown-menu-right',
                ]),
                'content' => join('', $menuItems),
            ]);
        }

        return false;
    }
}
