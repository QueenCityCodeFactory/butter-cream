<?php
declare(strict_types=1);

namespace ButterCream\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CategoriesFixture
 */
class CategoriesFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'categories';

    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            ['id' => 1, 'parent_id' => null, 'name' => 'Root 1'],
            ['id' => 2, 'parent_id' => 1, 'name' => 'Child 1-1'],
            ['id' => 3, 'parent_id' => 1, 'name' => 'Child 1-2'],
            ['id' => 4, 'parent_id' => null, 'name' => 'Root 2'],
            ['id' => 5, 'parent_id' => 2, 'name' => 'Grandchild 1-1-1'],
        ];
        parent::init();
    }
}
