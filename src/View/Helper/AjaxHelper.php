<?php
declare(strict_types=1);

namespace ButterCream\View\Helper;

use Cake\Routing\Router;
use Cake\View\Helper;
use Cake\View\StringTemplateTrait;

/**
 * Ajax Helper
 */
class AjaxHelper extends Helper
{
    use StringTemplateTrait;

    /**
     * Default config for the helper.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'templates' => [
            'tag' => '<{{tag}}{{attrs}}>{{content}}</{{tag}}>',
        ],
    ];

    /**
     * Creates a HTML Tag with correct attributes for Ajax Pagination if user has access
     *
     * ### Options:
     *
     * - `tag`
     * - `content`
     * - `class`
     *
     * @param string $domId - the HTML DOM ID
     * @param string|array $url - Router::url() format
     * @param array $options - options for the HTML Element
     * @return string
     */
    public function relatedData(string $domId, $url, array $options = []): string
    {
        $options += [
            'tag' => 'div',
            'content' => null,
            'class' => 'ajax-pagination',
            'id' => $domId,
            'data-url' => Router::url($url, true)
        ];
        $name = $options['tag'];
        unset($options['tag']);

        $content = $options['content'];
        unset($options['content']);

        return $this->formatTemplate('tag', [
            'tag' => $name,
            'attrs' => $this->templater()->formatAttributes($options),
            'content' => $content,
        ]);
    }
}
