<?php
declare(strict_types=1);

namespace ButterCream\Test\TestCase\Database\Type;

use ButterCream\Database\Type\JsonArrayType;
use Cake\Database\Driver\Sqlite;
use Cake\TestSuite\TestCase;

/**
 * ButterCream\Database\Type\JsonArrayType Test Case
 */
class JsonArrayTypeTest extends TestCase
{
    /**
     * @var \ButterCream\Database\Type\JsonArrayType
     */
    protected JsonArrayType $type;

    /**
     * @var \Cake\Database\Driver\Sqlite
     */
    protected Sqlite $driver;

    /**
     * setUp
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->type = new JsonArrayType('json_array');
        $this->driver = $this->createStub(Sqlite::class);
    }

    /**
     * Test toPHP with valid JSON string
     *
     * @return void
     */
    public function testToPHPValidJson(): void
    {
        $result = $this->type->toPHP('{"name":"test","value":123}', $this->driver);

        $this->assertIsObject($result);
        $this->assertEquals('test', $result->name);
        $this->assertEquals(123, $result->value);
    }

    /**
     * Test toPHP with JSON array
     *
     * @return void
     */
    public function testToPHPJsonArray(): void
    {
        $result = $this->type->toPHP('[1,2,3]', $this->driver);

        $this->assertIsArray($result);
        $this->assertEquals([1, 2, 3], $result);
    }

    /**
     * Test toPHP with non-string returns null
     *
     * @return void
     */
    public function testToPHPNonString(): void
    {
        $this->assertNull($this->type->toPHP(null, $this->driver));
        $this->assertNull($this->type->toPHP(123, $this->driver));
    }

    /**
     * Test toPHP with invalid JSON returns null
     *
     * @return void
     */
    public function testToPHPInvalidJson(): void
    {
        $result = $this->type->toPHP('{invalid json}', $this->driver);
        $this->assertNull($result);
    }

    /**
     * Test manyToPHP with multiple fields
     *
     * @return void
     */
    public function testManyToPHP(): void
    {
        $values = [
            'config' => '{"key":"value"}',
            'tags' => '["tag1","tag2"]',
            'name' => 'John',
        ];
        $fields = ['config', 'tags'];

        $result = $this->type->manyToPHP($values, $fields, $this->driver);

        $this->assertIsObject($result['config']);
        $this->assertEquals('value', $result['config']->key);
        $this->assertIsArray($result['tags']);
        $this->assertEquals(['tag1', 'tag2'], $result['tags']);
        $this->assertEquals('John', $result['name']); // Unchanged
    }

    /**
     * Test manyToPHP skips null fields
     *
     * @return void
     */
    public function testManyToPHPSkipsNulls(): void
    {
        $values = [
            'config' => null,
            'tags' => '["tag1"]',
        ];
        $fields = ['config', 'tags'];

        $result = $this->type->manyToPHP($values, $fields, $this->driver);

        $this->assertNull($result['config']);
        $this->assertIsArray($result['tags']);
    }
}
