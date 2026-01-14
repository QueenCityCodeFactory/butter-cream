<?php
declare(strict_types=1);

namespace ButterCream\Test\TestCase\Utility;

use ButterCream\Utility\Format;
use Cake\TestSuite\TestCase;

/**
 * ButterCream\Utility\Format Test Case
 */
class FormatTest extends TestCase
{
    /**
     * Test ssn method
     *
     * @return void
     */
    public function testSsn(): void
    {
        $result = Format::ssn('123456789');
        $this->assertEquals('123-45-6789', $result);

        $result = Format::ssn('123-45-6789');
        $this->assertEquals('123-45-6789', $result);

        // With already formatted data
        $result = Format::ssn('123.45.6789');
        $this->assertEquals('123-45-6789', $result);
    }

    /**
     * Test zip method
     *
     * @return void
     */
    public function testZip(): void
    {
        // 5 digit
        $result = Format::zip('12345');
        $this->assertEquals('12345', $result);

        // 9 digit
        $result = Format::zip('123456789');
        $this->assertEquals('12345-6789', $result);

        // Already formatted
        $result = Format::zip('12345-6789');
        $this->assertEquals('12345-6789', $result);

        // Invalid length returns empty
        $result = Format::zip('123');
        $this->assertEquals('', $result);
    }

    /**
     * Test phone method
     *
     * @return void
     */
    public function testPhone(): void
    {
        // 10 digit
        $result = Format::phone('5551234567');
        $this->assertEquals('(555) 123-4567', $result);

        // 7 digit
        $result = Format::phone('1234567');
        $this->assertEquals('123-4567', $result);

        // With extension
        $result = Format::phone('5551234567 ext 123');
        $this->assertEquals('(555) 123-4567 x123', $result);

        // Already formatted
        $result = Format::phone('(555) 123-4567');
        $this->assertEquals('(555) 123-4567', $result);
    }

    /**
     * Test parsePhone method returns string by default
     *
     * @return void
     */
    public function testParsePhoneString(): void
    {
        $result = Format::parsePhone('(555) 123-4567');
        $this->assertIsString($result);
        $this->assertEquals('5551234567', $result);
    }

    /**
     * Test parsePhone method with returnBoth parameter
     *
     * @return void
     */
    public function testParsePhoneBoth(): void
    {
        $result = Format::parsePhone('(555) 123-4567 x123', true);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('parts', $result);
        $this->assertArrayHasKey('string', $result);
        
        $this->assertEquals('555', $result['parts']['area']);
        $this->assertEquals('123', $result['parts']['exchange']);
        $this->assertEquals('4567', $result['parts']['number']);
        $this->assertEquals('123', $result['parts']['ext']);
        $this->assertEquals('5551234567', $result['string']);
    }

    /**
     * Test formatString method
     *
     * @return void
     */
    public function testFormatString(): void
    {
        $result = Format::formatString('1234567890', '(000) 000-0000');
        $this->assertEquals('(123) 456-7890', $result);

        $result = Format::formatString('123456789', '000-00-0000');
        $this->assertEquals('123-45-6789', $result);

        // Empty format returns original
        $result = Format::formatString('123', '');
        $this->assertEquals('123', $result);

        // Empty string returns original
        $result = Format::formatString('', '000-000');
        $this->assertEquals('', $result);
    }

    /**
     * Test maskString method
     *
     * @return void
     */
    public function testMaskString(): void
    {
        // maskString works similarly to formatString
        // Testing basic functionality - empty values
        $result = Format::maskString('', '000');
        $this->assertEquals('', $result);

        $result = Format::maskString('123', '');
        $this->assertEquals('123', $result);
    }
}
