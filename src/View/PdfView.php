<?php
declare(strict_types=1);

namespace ButterCream\View;

use CakePdf\View\PdfView as View;

/**
 * Application PDF View
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
class PdfView extends View
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading helpers.
     *
     * e.g. `$this->loadHelper('Html');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadHelper('Ajax', [
            'className' => 'ButterCream.Ajax',
        ]);
        $this->loadHelper('Flash', [
            'className' => 'ButterCream.Flash',
        ]);
        $this->loadHelper('Form', [
            'className' => 'ButterCream.Form',
            'templates' => [
                'dateWidget' => '
                    <ul class="list-inline">
                        <li class="month">{{month}}</li>
                        <li class="day">{{day}}</li>
                        <li class="year">{{year}}</li>
                        <li class="hour">{{hour}}</li>
                        <li class="minute">{{minute}}</li>
                        <li class="second">{{second}}</li>
                        <li class="meridian">{{meridian}}</li>
                    </ul>
                ',
            ],
        ]);
        $this->addHelper('Format', [
            'className' => 'ButterCream.Format',
        ]);
        $this->loadHelper('Html', [
            'className' => 'ButterCream.Html',
        ]);
        $this->loadHelper('Paginator', [
            'className' => 'ButterCream.Paginator',
        ]);
        $this->loadHelper('Table', [
            'className' => 'ButterCream.Table',
        ]);
        $this->loadHelper('Time', [
            'className' => 'ButterCream.Time',
        ]);
        $this->loadHelper('Url', [
            'className' => 'ButterCream.Url',
        ]);
    }
}
