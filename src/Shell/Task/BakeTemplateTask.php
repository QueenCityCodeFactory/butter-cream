<?php
namespace ButterCream\Shell\Task;

use Bake\Shell\Task\BakeTemplateTask as CakeBakeTemplateTask;
use Bake\View\BakeView;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Http\Response;
use Cake\Http\ServerRequest as Request;

class BakeTemplateTask extends CakeBakeTemplateTask
{

    /**
     * Get view instance
     *
     * @return \Cake\View\View
     * @triggers Bake.initialize $view
     */
    public function getView()
    {
        if ($this->View) {
            return $this->View;
        }

        $theme = isset($this->params['theme']) ? $this->params['theme'] : '';

        $viewOptions = [
            'helpers' => [
                'ButterCream.Bake',
                'Bake.DocBlock'
            ],
            'theme' => $theme
        ];

        $view = new BakeView(new Request(), new Response(), null, $viewOptions);
        $event = new Event('Bake.initialize', $view);
        EventManager::instance()->dispatch($event);
        $this->View = $event->getSubject();

        return $this->View;
    }
}
