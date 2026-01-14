<?php
/**
 * Test fixture for Categories
 */
return [
    'table' => 'categories',
    'columns' => [
        'id' => ['type' => 'integer'],
        'parent_id' => ['type' => 'integer', 'null' => true],
        'name' => ['type' => 'string', 'length' => 255],
        '_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
    ],
    'records' => [
        ['id' => 1, 'parent_id' => null, 'name' => 'Root 1'],
        ['id' => 2, 'parent_id' => 1, 'name' => 'Child 1-1'],
        ['id' => 3, 'parent_id' => 1, 'name' => 'Child 1-2'],
        ['id' => 4, 'parent_id' => null, 'name' => 'Root 2'],
        ['id' => 5, 'parent_id' => 2, 'name' => 'Grandchild 1-1-1'],
    ],
];
