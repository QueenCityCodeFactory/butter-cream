<?php
declare(strict_types=1);

namespace ButterCream\View\Helper;

use BootstrapUI\View\Helper\FlashHelper as Helper;

/**
 * FlashHelper class to render flash messages.
 */
class FlashHelper extends Helper
{
    /**
     * Default config
     *
     * - class: List of classes to be applied to the div containing message
     * - attributes: Additional attributes for the div containing message
     * - icon: A boolean defining whether to use icons, or a string holding an icon name or HTML.
     * - iconMap: A map of flash element names and icon definitions.
     *
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [
        'class' => ['alert', 'alert-dismissible', 'fade', 'show', 'd-flex', 'align-items-center'],
        'attributes' => ['role' => 'alert'],
        'icon' => true,
        'iconMap' => [
            'default' => 'info-circle',
            'success' => 'check-circle',
            'error' => 'exclamation-triangle',
            'info' => 'info-circle',
            'warning' => 'exclamation-triangle',
        ],
        'element' => 'BootstrapUI.flash/default',
    ];
}
