<?php
declare(strict_types=1);

namespace ButterCream\Test\TestCase\Model;

use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;

/**
 * ButterCream\Model\TreeviewTrait Test Case
 */
class TreeviewTraitTest extends TestCase
{
    /**
     * @var \ButterCream\Test\TestCase\Model\CategoriesTable
     */
    protected CategoriesTable $Categories;

    /**
     * setUp
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $connection = ConnectionManager::get('test');
        $connection->execute('CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY,
            parent_id INTEGER NULL,
            name VARCHAR(255) NOT NULL
        )');
        $connection->execute('DELETE FROM categories');
        $connection->execute("INSERT INTO categories (id, parent_id, name) VALUES (1, NULL, 'Root 1')");
        $connection->execute("INSERT INTO categories (id, parent_id, name) VALUES (2, 1, 'Child 1-1')");
        $connection->execute("INSERT INTO categories (id, parent_id, name) VALUES (3, 1, 'Child 1-2')");
        $connection->execute("INSERT INTO categories (id, parent_id, name) VALUES (4, NULL, 'Root 2')");
        $connection->execute("INSERT INTO categories (id, parent_id, name) VALUES (5, 2, 'Grandchild 1-1-1')");

        $this->Categories = new CategoriesTable([
            'connection' => $connection,
        ]);
    }

    /**
     * tearDown
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $connection = ConnectionManager::get('test');
        $connection->execute('DROP TABLE IF EXISTS categories');
        parent::tearDown();
    }

    /**
     * Test findTreeview returns nested results
     *
     * @return void
     */
    public function testFindTreeviewBasic(): void
    {
        $results = $this->Categories->find(
            'treeview',
            keyField: 'id',
            parentField: 'parent_id',
        )->toArray();

        // Should have 2 root-level items (parent_id = null)
        $this->assertCount(2, $results);
    }

    /**
     * Test findTreeview nests children properly
     *
     * @return void
     */
    public function testFindTreeviewNesting(): void
    {
        $results = $this->Categories->find(
            'treeview',
            keyField: 'id',
            parentField: 'parent_id',
        )->toArray();

        // Root 1 should have 2 children
        $root1 = $results[0];
        $this->assertEquals('Root 1', $root1['name']);
        $this->assertCount(2, $root1['nodes']);

        // Child 1-1 should have 1 grandchild
        $child1 = $root1['nodes'][0];
        $this->assertEquals('Child 1-1', $child1['name']);
        $this->assertCount(1, $child1['nodes']);

        // Grandchild
        $this->assertEquals('Grandchild 1-1-1', $child1['nodes'][0]['name']);
    }

    /**
     * Test findTreeview with custom nesting key
     *
     * @return void
     */
    public function testFindTreeviewCustomNestingKey(): void
    {
        $results = $this->Categories->find(
            'treeview',
            keyField: 'id',
            parentField: 'parent_id',
            nestingKey: 'children',
        )->toArray();

        $root1 = $results[0];
        $this->assertArrayHasKey('children', $root1);
        $this->assertCount(2, $root1['children']);
    }

    /**
     * Test Root 2 has no children
     *
     * @return void
     */
    public function testFindTreeviewLeafNode(): void
    {
        $results = $this->Categories->find(
            'treeview',
            keyField: 'id',
            parentField: 'parent_id',
        )->toArray();

        $root2 = $results[1];
        $this->assertEquals('Root 2', $root2['name']);
        $this->assertEmpty($root2['nodes']);
    }
}
