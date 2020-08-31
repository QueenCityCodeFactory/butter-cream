<?php
declare(strict_types=1);

namespace ButterCream\Controller;

use Cake\Controller\Controller as CakeController;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use RuntimeException;

class Controller extends CakeController
{
    /**
     * Ajax Pagination Limit
     *
     * @var int
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
            ],
        ]);
        $this->loadComponent('Paginator');
        $this->loadComponent('ButterCream.Flash');
        $this->loadComponent('ButterCream.Referer', [
            'ignored' => [
                '/login',
                '/logout',
            ],
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

    /**
     * Handles pagination of records in Table objects.
     *
     * Will load the referenced Table object, and have the PaginatorComponent
     * paginate the query using the request date and settings defined in `$this->paginate`.
     *
     * This method will also make the PaginatorHelper available in the view.
     *
     * @param \Cake\ORM\Table|string|\Cake\ORM\Query|null $object Table to paginate
     * (e.g: Table instance, 'TableName' or a Query object)
     * @param array $settings The settings/configuration used for pagination.
     * @return \Cake\ORM\ResultSet|\Cake\Datasource\ResultSetInterface|\Cake\Http\Response|null Query results
     * @link https://book.cakephp.org/4/en/controllers.html#paginating-a-model
     * @throws \RuntimeException When no compatible table object can be found.
     */
    public function paginate($object = null, array $settings = [])
    {
        if (is_object($object)) {
            $table = $object;
        }

        if (is_string($object) || $object === null) {
            $try = [$object, $this->modelClass];
            foreach ($try as $tableName) {
                if (empty($tableName)) {
                    continue;
                }
                $table = $this->loadModel($tableName);
                break;
            }
        }

        $this->loadComponent('Paginator');
        if (empty($table)) {
            throw new RuntimeException('Unable to locate an object compatible with paginate.');
        }
        $settings += $this->paginate;

        try {
            return $this->Paginator->paginate($table, $settings);
        } catch (NotFoundException $e) {
            $request = $this->getRequest();
            $queryString = $request->getQueryParams();
            if (isset($queryString['page'])) {
                $queryString['page'] = 1;
            }

            return $this->redirect([
                'plugin' => $request->getParam('plugin'),
                'prefix' => $request->getParam('prefix'),
                'controller' => $request->getParam('controller'),
                'action' => $request->getParam('action'),
                '?' => $queryString,
            ]);
        }
    }
}
