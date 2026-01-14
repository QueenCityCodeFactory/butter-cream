<?php
declare(strict_types=1);

namespace ButterCream\Test\TestCase\Model;

use ButterCream\Model\Validation;
use Cake\I18n\DateTime;
use Cake\TestSuite\TestCase;

/**
 * ButterCream\Model\Validation Test Case
 */
class ValidationTest extends TestCase
{
    /**
     * Test phone method with valid US phone numbers
     *
     * @return void
     */
    public function testPhoneValid(): void
    {
        // Test valid 10-digit phone with specific area/exchange codes
        $this->assertTrue(Validation::phone('2125551234'));
        $this->assertTrue(Validation::phone('312 555 1234'));
    }

    /**
     * Test phone method with invalid phone numbers
     *
     * @return void
     */
    public function testPhoneInvalid(): void
    {
        $this->assertFalse(Validation::phone('123456'));
        $this->assertFalse(Validation::phone('abc-def-ghij'));
        $this->assertFalse(Validation::phone(''));
    }

    /**
     * Test postal method with valid US ZIP codes
     *
     * @return void
     */
    public function testPostalValid(): void
    {
        $this->assertTrue(Validation::postal('12345'));
        $this->assertTrue(Validation::postal('12345-6789'));
        $this->assertTrue(Validation::postal('123456789'));
    }

    /**
     * Test postal method with invalid ZIP codes
     *
     * @return void
     */
    public function testPostalInvalid(): void
    {
        $this->assertFalse(Validation::postal('1234'));
        $this->assertFalse(Validation::postal('abcde'));
        $this->assertFalse(Validation::postal(''));
    }

    /**
     * Test ssn method with valid US Social Security Numbers
     *
     * @return void
     */
    public function testSsnValid(): void
    {
        $this->assertTrue(Validation::ssn('123456789'));
        $this->assertTrue(Validation::ssn('123-45-6789'));
    }

    /**
     * Test ssn method with invalid SSNs
     *
     * @return void
     */
    public function testSsnInvalid(): void
    {
        $this->assertFalse(Validation::ssn('12345678'));
        $this->assertFalse(Validation::ssn('abc-de-fghi'));
        $this->assertFalse(Validation::ssn(''));
    }

    /**
     * Test birthdate method with valid dates
     *
     * @return void
     */
    public function testBirthdateValid(): void
    {
        // Past date
        $pastDate = DateTime::now()->subYears(25);
        $this->assertTrue(Validation::birthdate($pastDate));

        // Today's date
        $today = DateTime::now();
        $this->assertTrue(Validation::birthdate($today));
    }

    /**
     * Test birthdate method with future dates
     *
     * @return void
     */
    public function testBirthdateInvalid(): void
    {
        // Future date
        $futureDate = DateTime::now()->addDays(1);
        $this->assertFalse(Validation::birthdate($futureDate));

        $futureDate = DateTime::now()->addYears(1);
        $this->assertFalse(Validation::birthdate($futureDate));
    }
}
