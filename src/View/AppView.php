<?php
declare(strict_types=1);

namespace ButterCream\View;

use Cake\View\View;

/**
 * Application View
 *
 * @property \ButterCream\View\Helper\AjaxHelper $Ajax
 * @property \ButterCream\View\Helper\FlashHelper $Flash
 * @property \ButterCream\View\Helper\FormHelper $Form
 * @property \ButterCream\View\Helper\FormatHelper $Format
 * @property \ButterCream\View\Helper\HtmlHelper $Html
 * @property \ButterCream\View\Helper\PaginatorHelper $Paginator
 * @property \ButterCream\View\Helper\TableHelper $Table
 * @property \ButterCream\View\Helper\TimeHelper $Time
 * @property \ButterCream\View\Helper\UrlHelper $Url
 */
class AppView extends View
{
    use ButterCreamHelpersTrait;

    /**
     * Initialization hook method.
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->loadButterCreamHelpers();
    }
}
