<?php
declare(strict_types=1);

namespace ButterCream\Test\TestCase\Message\Exception;

use ButterCream\Message\Exception\StatusMessageException;
use Cake\TestSuite\TestCase;

/**
 * ButterCream\Message\Exception\StatusMessageException Test Case
 */
class StatusMessageExceptionTest extends TestCase
{
    /**
     * Test exception with valid key
     *
     * @return void
     */
    public function testExceptionWithValidKey(): void
    {
        $exception = new StatusMessageException('missing_field');

        $this->assertEquals('Missing Field!', $exception->getMessage());
        $this->assertEquals(400, $exception->getCode());
    }

    /**
     * Test exception with invalid key falls back to defaults
     *
     * @return void
     */
    public function testExceptionWithInvalidKey(): void
    {
        $exception = new StatusMessageException('non_existent_key');

        $this->assertStringContainsString('invalid', strtolower($exception->getMessage()));
        $this->assertEquals(500, $exception->getCode());
    }

    /**
     * Test exception with null key
     *
     * @return void
     */
    public function testExceptionWithNullKey(): void
    {
        $exception = new StatusMessageException(null);

        $this->assertEquals(500, $exception->getCode());
    }

    /**
     * Test exception with file service key
     *
     * @return void
     */
    public function testExceptionWithFileServiceKey(): void
    {
        $exception = new StatusMessageException('file_service_missing_tmp_file');

        $this->assertStringContainsString('tmp', strtolower($exception->getMessage()));
        $this->assertEquals(500, $exception->getCode());
    }
}
