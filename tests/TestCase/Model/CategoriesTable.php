<?php
declare(strict_types=1);

namespace ButterCream\Test\TestCase\Model;

use ButterCream\Model\TreeviewTrait;
use Cake\ORM\Table;

/**
 * Concrete Table using TreeviewTrait for testing
 */
class CategoriesTable extends Table
{
    use TreeviewTrait;

    /**
     * @param array $config Table configuration
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('categories');
        $this->setPrimaryKey('id');
    }
}
