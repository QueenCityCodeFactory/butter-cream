<?php
declare(strict_types=1);

namespace ButterCream\Test\TestCase\View\Helper;

use ButterCream\View\Helper\FormatHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * ButterCream\View\Helper\FormatHelper Test Case
 */
class FormatHelperTest extends TestCase
{
    /**
     * @var \ButterCream\View\Helper\FormatHelper
     */
    protected $Format;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $request = new \Cake\Http\ServerRequest(['url' => '/']);
        $response = new \Cake\Http\Response(['charset' => 'UTF-8']);
        $view = new View($request, $response);
        $this->Format = new FormatHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Format);
        parent::tearDown();
    }

    /**
     * Test ssn method
     *
     * @return void
     */
    public function testSsn(): void
    {
        $result = $this->Format->ssn('123456789');
        $this->assertEquals('123-45-6789', $result);

        $result = $this->Format->ssn('123-45-6789');
        $this->assertEquals('123-45-6789', $result);
    }

    /**
     * Test zip method with 5 digit
     *
     * @return void
     */
    public function testZipFiveDigit(): void
    {
        $result = $this->Format->zip('12345');
        $this->assertEquals('12345', $result);
    }

    /**
     * Test zip method with 9 digit
     *
     * @return void
     */
    public function testZipNineDigit(): void
    {
        $result = $this->Format->zip('123456789');
        $this->assertEquals('12345-6789', $result);

        $result = $this->Format->zip('12345-6789');
        $this->assertEquals('12345-6789', $result);
    }

    /**
     * Test phone method with 10 digit
     *
     * @return void
     */
    public function testPhoneTenDigit(): void
    {
        $result = $this->Format->phone('5551234567');
        $this->assertEquals('(555) 123-4567', $result);

        $result = $this->Format->phone('(555) 123-4567');
        $this->assertEquals('(555) 123-4567', $result);
    }

    /**
     * Test phone method with 7 digit
     *
     * @return void
     */
    public function testPhoneSevenDigit(): void
    {
        $result = $this->Format->phone('1234567');
        $this->assertEquals('123-4567', $result);
    }

    /**
     * Test phone method with extension
     *
     * @return void
     */
    public function testPhoneWithExtension(): void
    {
        $result = $this->Format->phone('5551234567 ext 123');
        $this->assertEquals('(555) 123-4567 x123', $result);

        $result = $this->Format->phone('5551234567x123');
        $this->assertEquals('(555) 123-4567 x123', $result);
    }

    /**
     * Test parsePhone method
     *
     * @return void
     */
    public function testParsePhone(): void
    {
        $result = $this->Format->parsePhone('(555) 123-4567');
        $this->assertEquals('5551234567', $result);

        $result = $this->Format->parsePhone('(555) 123-4567', true);
        $this->assertIsArray($result);
        $this->assertEquals('555', $result['parts']['area']);
        $this->assertEquals('123', $result['parts']['exchange']);
        $this->assertEquals('4567', $result['parts']['number']);
    }

    /**
     * Test formatString method
     *
     * @return void
     */
    public function testFormatString(): void
    {
        $result = $this->Format->formatString('1234567890', '(000) 000-0000');
        $this->assertEquals('(123) 456-7890', $result);
    }

    /**
     * Test maskString method
     *
     * @return void
     */
    public function testMaskString(): void
    {
        // Test basic maskString functionality
        $result = $this->Format->maskString('123', '');
        $this->assertEquals('123', $result);
    }
}
