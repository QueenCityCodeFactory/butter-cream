<?php
namespace ButterCream\View\Helper;

use BootstrapUI\View\Helper\PaginatorHelper as Helper;

class PaginatorHelper extends Helper
{

    /**
     * Set Ajax Link templates
     *
     * @param string $domId Html DOM ID
     * @param array $options list of options
     * @return void
     */
    public function ajaxTemplateOptions($domId, $options = [])
    {
        $options += ['class' => 'ajax-pagination-link'];

        $templates = [
            'nextActive' => '<li class="page-item next"><a class="page-link ' . $options['class'] . '" data-update="' . $domId . '" rel="next" aria-label="Next" href="{{url}}"><span aria-hidden="true">{{text}}</span></a></li>',
            'prevActive' => '<li class="page-item prev"><a class="page-link ' . $options['class'] . '" data-update="' . $domId . '" rel="prev" aria-label="Previous" href="{{url}}"><span aria-hidden="true">{{text}}</span></a></li>',
            'first' => '<li class="page-item first"><a class="page-link ' . $options['class'] . '" data-update="' . $domId . '" href="{{url}}">{{text}}</a></li>',
            'last' => '<li class="page-item last"><a class="page-link ' . $options['class'] . '" data-update="' . $domId . '" href="{{url}}">{{text}}</a></li>',
            'number' => '<li class="page-item"><a class="page-link ' . $options['class'] . '" data-update="' . $domId . '" href="{{url}}">{{text}}</a></li>',
            'sort' => '<a class="' . $options['class'] . '" data-update="' . $domId . '" href="{{url}}">{{text}}</a>',
            'sortAsc' => '<a class="asc ' . $options['class'] . '" data-update="' . $domId . '" href="{{url}}">{{text}}</a>',
            'sortDesc' => '<a class="desc ' . $options['class'] . '" data-update="' . $domId . '" href="{{url}}">{{text}}</a>',
            'sortAscLocked' => '<a class="asc locked ' . $options['class'] . '" data-update="' . $domId . '" href="{{url}}">{{text}}</a>',
            'sortDescLocked' => '<a class="desc locked' . $options['class'] . '" data-update="' . $domId . '" href="{{url}}">{{text}}</a>',
        ];
        $this->templater()->add($templates);
    }

    /**
     * Generates a "previous" link for a set of paged records
     *
     * ### Options:
     *
     * - `disabledTitle` The text to used when the link is disabled. This
     *   defaults to the same text at the active link. Setting to false will cause
     *   this method to return ''.
     * - `escape` Whether you want the contents html entity encoded, defaults to true
     * - `model` The model to use, defaults to PaginatorHelper::defaultModel()
     * - `url` An array of additional URL options to use for link generation.
     *
     * @param string $title Title for the link. Defaults to '<< Previous'.
     * @param array $options Options for pagination link. See above for list of keys.
     * @return string A "previous" link or a disabled link.
     * @link http://book.cakephp.org/3.0/en/views/helpers/paginator.html#creating-jump-links
     */
    public function prev($title = '<em class="fas fa-angle-left"></em>', array $options = [])
    {
        $defaults = [
            'escape' => false,
        ];
        $options += $defaults;

        return parent::prev($title, $options);
    }

    /**
     * Generates a "next" link for a set of paged records
     *
     * ### Options:
     *
     * - `disabledTitle` The text to used when the link is disabled. This
     *   defaults to the same text at the active link. Setting to false will cause
     *   this method to return ''.
     * - `escape` Whether you want the contents html entity encoded, defaults to true
     * - `model` The model to use, defaults to PaginatorHelper::defaultModel()
     * - `url` An array of additional URL options to use for link generation.
     *
     * @param string $title Title for the link. Defaults to 'Next >>'.
     * @param array $options Options for pagination link. See above for list of keys.
     * @return string A "next" link or $disabledTitle text if the link is disabled.
     * @link http://book.cakephp.org/3.0/en/views/helpers/paginator.html#creating-jump-links
     */
    public function next($title = '<em class="fas fa-angle-right"></em>', array $options = [])
    {
        $defaults = [
            'escape' => false,
        ];
        $options += $defaults;

        return parent::next($title, $options);
    }

    /**
     * Returns a set of numbers for the paged result set.
     *
     * In addition to the numbers, the method can also generate previous and next
     * links using additional options as shown below which are not available in
     * CakePHP core's PaginatorHelper::numbers().
     *
     * ### Options
     *
     * - `prev` If set generates "previous" link. Can be `true` or string.
     * - `next` If set generates "next" link. Can be `true` or string.
     * - `size` Used to control sizing class added to UL tag. For eg.
     *   using `'size' => 'lg'` would add class `pagination-lg` to UL tag.
     *
     * @param array $options Options for the numbers.
     * @return string Numbers string.
     * @link http://book.cakephp.org/3.0/en/views/helpers/paginator.html#creating-page-number-links
     */
    public function numbers(array $options = [])
    {
        $defaults = [
            'before' => false,
            'after' => false,
        ];
        $options += $defaults;

        return parent::numbers($options);
    }
}
