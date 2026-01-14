<?php
declare(strict_types=1);

namespace ButterCream\Controller;

use Cake\Controller\Controller as CakeController;
use Cake\Core\App;
use Cake\Datasource\Paging\Exception\PageOutOfBoundsException;
use Cake\Datasource\Paging\NumericPaginator;
use Cake\Datasource\Paging\PaginatedInterface;
use Cake\Datasource\QueryInterface;
use Cake\Datasource\RepositoryInterface;
use Cake\Event\EventInterface;
use CakePdf\View\PdfView;
use CakeSpreadsheet\View\SpreadsheetView;

class Controller extends CakeController
{
    /**
     * Ajax Pagination Limit
     *
     * @var int
     */
    public int $ajaxPaginationLimit = 5;

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

        $this->loadComponent('ButterCream.Flash');
        $this->loadComponent('ButterCream.Referer', [
            'ignored' => [
                '/login',
                '/logout',
            ],
        ]);

        $this->addViewClasses([PdfView::class, SpreadsheetView::class]);
    }

    /**
     * Called before the controller action. You can use this method to configure and customize components
     * or perform logic that needs to happen before each controller action.
     *
     * @param \Cake\Event\EventInterface $event An Event instance
     * @return \Cake\Http\Response|null|void
     * @link https://book.cakephp.org/5/en/controllers.html#request-life-cycle-callbacks
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
     * @link https://book.cakephp.org/5/en/controllers.html#request-life-cycle-callbacks
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
     * Will load the referenced Table object, and have the paginator
     * paginate the query using the request date and settings defined in `$this->paginate`.
     *
     * This method will also make the PaginatorHelper available in the view.
     *
     * @param \Cake\Datasource\RepositoryInterface|\Cake\Datasource\QueryInterface|string|null $object Table to paginate
     * (e.g: Table instance, 'TableName' or a Query object)
     * @param array<string, mixed> $settings The settings/configuration used for pagination.
     * See {@link \Cake\Controller\Controller::$paginate}.
     * @return \Cake\Datasource\Paging\PaginatedInterface
     * @link https://book.cakephp.org/5/en/controllers.html#paginating-a-model
     * @throws \Cake\Http\Exception\NotFoundException When a page out of bounds is requested.
     */
    public function paginate(
        RepositoryInterface|QueryInterface|string|null $object = null,
        array $settings = [],
    ): PaginatedInterface {
        if (!is_object($object)) {
            $object = $this->fetchTable($object);
        }

        $settings += $this->paginate;

        /** @var class-string<\Cake\Datasource\Paging\PaginatorInterface> $paginator */
        $paginator = App::className(
            $settings['className'] ?? NumericPaginator::class,
            'Datasource/Paging',
            'Paginator',
        );
        $paginator = new $paginator();
        unset($settings['className']);

        try {
            $results = $paginator->paginate(
                $object,
                $this->request->getQueryParams(),
                $settings,
            );
        } catch (PageOutOfBoundsException $exception) {
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

        return $results;
    }
}
