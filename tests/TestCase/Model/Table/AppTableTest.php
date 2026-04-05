<?php
declare(strict_types=1);

namespace ButterCream\Test\TestCase\Model\Table;

use ArrayObject;
use ButterCream\Model\Table\AppTable;
use Cake\Event\Event;
use Cake\TestSuite\TestCase;

/**
 * ButterCream\Model\Table\AppTable Test Case
 */
class AppTableTest extends TestCase
{
    /**
     * @var \ButterCream\Model\Table\AppTable
     */
    protected AppTable $AppTable;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->AppTable = new AppTable();
    }

    /**
     * Test cleanData replaces smart quotes with standard characters
     *
     * @return void
     */
    public function testBeforeMarshalCleansSmartQuotes(): void
    {
        $data = new ArrayObject([
            'name' => "He said \xE2\x80\x9CHello\xE2\x80\x9D",
            'description' => "It\xE2\x80\x99s a test",
        ]);
        $options = new ArrayObject();
        $event = new Event('Model.beforeMarshal');

        $this->AppTable->beforeMarshal($event, $data, $options);

        $this->assertEquals('He said "Hello"', $data['name']);
        $this->assertEquals("It's a test", $data['description']);
    }

    /**
     * Test cleanData trims whitespace but preserves newlines
     *
     * @return void
     */
    public function testBeforeMarshalTrimsWhitespace(): void
    {
        $data = new ArrayObject([
            'name' => "  Hello World  \t",
            'body' => "Line 1\nLine 2\r\nLine 3",
        ]);
        $options = new ArrayObject();
        $event = new Event('Model.beforeMarshal');

        $this->AppTable->beforeMarshal($event, $data, $options);

        $this->assertEquals('Hello World', $data['name']);
        // Newlines should be preserved
        $this->assertStringContainsString("\n", $data['body']);
    }

    /**
     * Test cleanData handles nested arrays
     *
     * @return void
     */
    public function testBeforeMarshalHandlesNestedArrays(): void
    {
        $data = new ArrayObject([
            'address' => [
                'street' => '  123 Main St  ',
                'city' => '  Springfield  ',
            ],
        ]);
        $options = new ArrayObject();
        $event = new Event('Model.beforeMarshal');

        $this->AppTable->beforeMarshal($event, $data, $options);

        $this->assertEquals('123 Main St', $data['address']['street']);
        $this->assertEquals('Springfield', $data['address']['city']);
    }

    /**
     * Test cleanData replaces em dashes
     *
     * @return void
     */
    public function testBeforeMarshalReplacesEmDash(): void
    {
        $data = new ArrayObject([
            'text' => "Hello\xE2\x80\x94World",
        ]);
        $options = new ArrayObject();
        $event = new Event('Model.beforeMarshal');

        $this->AppTable->beforeMarshal($event, $data, $options);

        $this->assertEquals('Hello-World', $data['text']);
    }

    /**
     * Test cleanData replaces ellipsis
     *
     * @return void
     */
    public function testBeforeMarshalReplacesEllipsis(): void
    {
        $data = new ArrayObject([
            'text' => "Wait\xE2\x80\xA6",
        ]);
        $options = new ArrayObject();
        $event = new Event('Model.beforeMarshal');

        $this->AppTable->beforeMarshal($event, $data, $options);

        $this->assertEquals('Wait...', $data['text']);
    }

    /**
     * Test cleanData does not modify non-string values
     *
     * @return void
     */
    public function testBeforeMarshalIgnoresNonStrings(): void
    {
        $data = new ArrayObject([
            'count' => 42,
            'active' => true,
            'tags' => null,
        ]);
        $options = new ArrayObject();
        $event = new Event('Model.beforeMarshal');

        $this->AppTable->beforeMarshal($event, $data, $options);

        $this->assertEquals(42, $data['count']);
        $this->assertTrue($data['active']);
        $this->assertNull($data['tags']);
    }
}
