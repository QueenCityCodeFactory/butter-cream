<?php
declare(strict_types=1);

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
    public function ajaxTemplateOptions(string $domId, array $options = [])
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
     * - `templates` An array of templates, or template file name containing the
     *   templates you'd like to use when generating the link for previous page.
     *   The helper's original templates will be restored once prev() is done.
     *
     * @param string $title Title for the link. Defaults to '<< Previous'.
     * @param array $options Options for pagination link. See above for list of keys.
     * @return string A "previous" link or a disabled link.
     * @link https://book.cakephp.org/4/en/views/helpers/paginator.html#creating-jump-links
     */
    public function prev(string $title = '<em class="fas fa-angle-left"></em>', array $options = []): string
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
     * - `templates` An array of templates, or template file name containing the
     *   templates you'd like to use when generating the link for next page.
     *   The helper's original templates will be restored once next() is done.
     *
     * @param string $title Title for the link. Defaults to 'Next >>'.
     * @param array $options Options for pagination link. See above for list of keys.
     * @return string A "next" link or $disabledTitle text if the link is disabled.
     * @link https://book.cakephp.org/4/en/views/helpers/paginator.html#creating-jump-links
     */
    public function next(string $title = '<em class="fas fa-angle-right"></em>', array $options = []): string
    {
        $defaults = [
            'escape' => false,
        ];
        $options += $defaults;

        return parent::next($title, $options);
    }

    /**
     * Returns a set of numbers for the paged result set
     * uses a modulus to decide how many numbers to show on each side of the current page (default: 8).
     *
     * ```
     * $this->Paginator->numbers(['first' => 2, 'last' => 2]);
     * ```
     *
     * Using the first and last options you can create links to the beginning and end of the page set.
     *
     * ### Options
     *
     * - `before` Content to be inserted before the numbers, but after the first links.
     * - `after` Content to be inserted after the numbers, but before the last links.
     * - `model` Model to create numbers for, defaults to PaginatorHelper::defaultModel()
     * - `modulus` How many numbers to include on either side of the current page, defaults to 8.
     *    Set to `false` to disable and to show all numbers.
     * - `first` Whether you want first links generated, set to an integer to define the number of 'first'
     *    links to generate. If a string is set a link to the first page will be generated with the value
     *    as the title.
     * - `last` Whether you want last links generated, set to an integer to define the number of 'last'
     *    links to generate. If a string is set a link to the last page will be generated with the value
     *    as the title.
     * - `templates` An array of templates, or template file name containing the templates you'd like to
     *    use when generating the numbers. The helper's original templates will be restored once
     *    numbers() is done.
     * - `url` An array of additional URL options to use for link generation.
     *
     * The generated number links will include the 'ellipsis' template when the `first` and `last` options
     * and the number of pages exceed the modulus. For example if you have 25 pages, and use the first/last
     * options and a modulus of 8, ellipsis content will be inserted after the first and last link sets.
     *
     * @param array $options Options for the numbers.
     * @return string Numbers string.
     * @link https://book.cakephp.org/4/en/views/helpers/paginator.html#creating-page-number-links
     */
    public function numbers(array $options = []): string
    {
        $defaults = [
            'before' => false,
            'after' => false,
        ];
        $options += $defaults;

        return parent::numbers($options);
    }
}
