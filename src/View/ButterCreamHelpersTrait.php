<?php
declare(strict_types=1);

namespace ButterCream\View;

/**
 * Loads the standard set of ButterCream view helpers.
 *
 * Use this trait in any View class that needs the ButterCream helper stack.
 */
trait ButterCreamHelpersTrait
{
    /**
     * Load the standard ButterCream helpers.
     *
     * Call this from your View's `initialize()` method.
     *
     * @return void
     */
    protected function loadButterCreamHelpers(): void
    {
        $this->addHelper('Ajax', [
            'className' => 'ButterCream.Ajax',
        ]);
        $this->addHelper('Alert', [
            'className' => 'ButterCream.Alert',
        ]);
        $this->addHelper('Badge', [
            'className' => 'ButterCream.Badge',
        ]);
        $this->addHelper('Card', [
            'className' => 'ButterCream.Card',
        ]);
        $this->addHelper('Filters', [
            'className' => 'ButterCream.Filters',
        ]);
        $this->addHelper('Flash', [
            'className' => 'ButterCream.Flash',
        ]);
        $this->addHelper('Form', [
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
        $this->addHelper('Html', [
            'className' => 'ButterCream.Html',
        ]);
        $this->addHelper('Paginator', [
            'className' => 'ButterCream.Paginator',
        ]);
        $this->addHelper('Progress', [
            'className' => 'ButterCream.Progress',
        ]);
        $this->addHelper('Table', [
            'className' => 'ButterCream.Table',
        ]);
        $this->addHelper('Time', [
            'className' => 'ButterCream.Time',
        ]);
        $this->addHelper('Url', [
            'className' => 'ButterCream.Url',
        ]);
    }
}
