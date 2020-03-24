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
     *
     * @var array
     */
    protected $_defaultConfig = [
        'class' => ['alert', 'alert-dismissible', 'fade', 'show'],
        'attributes' => ['role' => 'alert'],
        'element' => 'flash/default'
    ];
}
