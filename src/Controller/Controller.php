<?php
declare(strict_types=1);

namespace ButterCream\Controller;

use Cake\Controller\Controller as CakeController;
use Cake\Event\EventInterface;

class Controller extends CakeController
{

    /**
     * Ajax Pagination Limit
     *
     * @var boolean
     */
    public $ajaxPaginationLimit = 5;

    /**
     * Initialization hook method.
     *
     * Implement this method to avoid having to overwrite
     * the constructor and call parent.
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
            'viewClassMap' => [
                'xlsx' => 'CakeSpreadsheet.Excel',
                'pdf' => 'CakePdf.Pdf',
            ]
        ]);
        $this->loadComponent('ButterCream.Paginator');
        $this->loadComponent('ButterCream.Flash');
        $this->loadComponent('ButterCream.Referer', [
            'ignored' => [
                '/login',
                '/logout'
            ]
        ]);
    }

    /**
     * Called before the controller action. You can use this method to configure and customize components
     * or perform logic that needs to happen before each controller action.
     *
     * @param \Cake\Event\EventInterface $event An Event instance
     * @return \Cake\Http\Response|null|void
     * @link https://book.cakephp.org/4/en/controllers.html#request-life-cycle-callbacks
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
    }

    /**
     * Called after the controller action is run, but before the view is rendered. You can use this method
     * to perform logic or set view variables that are required on every request.
     *
     * @param \Cake\Event\EventInterface $event An Event instance
     * @return \Cake\Http\Response|null|void
     * @link https://book.cakephp.org/4/en/controllers.html#request-life-cycle-callbacks
     */
    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);

        if ($this->request->is('ajax')) {
            $this->paginate['limit'] = $this->ajaxPaginationLimit;
            $this->viewBuilder()->setLayout('ajax');
        }
    }
}
