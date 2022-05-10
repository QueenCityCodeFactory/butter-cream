<?php
declare(strict_types=1);

namespace ButterCream\Utility;

use Bake\Utility\TemplateRenderer as BakeTemplateRenderer;
use Bake\View\BakeView;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\View\View;

/**
 * Used by other tasks to generate templated output, Acts as an interface to BakeView
 */
class TemplateRenderer extends BakeTemplateRenderer
{
    /**
     * Get view instance
     *
     * @return \Cake\View\View
     * @triggers Bake.initialize $view
     */
    public function getView(): View
    {
        if ($this->view) {
            return $this->view;
        }

        $this->viewBuilder()
            ->addHelpers(['ButterCream.Bake', 'Bake.DocBlock'])
            ->setTheme($this->theme);

        $view = $this->createView(BakeView::class);
        $event = new Event('Bake.initialize', $view);
        EventManager::instance()->dispatch($event);
        /** @var \Bake\View\BakeView $view */
        $view = $event->getSubject();
        $this->view = $view;

        return $this->view;
    }
}
