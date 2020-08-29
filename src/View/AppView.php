<?php
declare(strict_types=1);

namespace ButterCream\View;

use Cake\View\View;

/**
 * Application View
 */
class AppView extends View
{
    /**
     * Initialization hook method.
     *
     * @return void
     */
    public function initialize(): void
    {
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
        $this->loadHelper('Format', [
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
