<?php
declare(strict_types=1);

namespace ButterCream\Test\TestCase\Message;

use ButterCream\Message\StatusMessage;
use Cake\TestSuite\TestCase;

/**
 * ButterCream\Message\StatusMessage Test Case
 */
class StatusMessageTest extends TestCase
{
    /**
     * Test getMessages returns all messages
     *
     * @return void
     */
    public function testGetMessages(): void
    {
        $messages = StatusMessage::getMessages();

        $this->assertIsArray($messages);
        $this->assertArrayHasKey('missing_field', $messages);
        $this->assertArrayHasKey('unauthorized', $messages);
        $this->assertArrayHasKey('file_service_missing_tmp_file', $messages);
    }

    /**
     * Test getMessage with valid key
     *
     * @return void
     */
    public function testGetMessageValid(): void
    {
        $message = StatusMessage::getMessage('missing_field');

        $this->assertIsArray($message);
        $this->assertEquals(400, $message['status']);
        $this->assertEquals('Missing Field!', $message['responseText']);
        $this->assertEquals('A100', $message['code']);
        $this->assertEquals('error', $message['type']);
    }

    /**
     * Test getMessage with invalid key returns false
     *
     * @return void
     */
    public function testGetMessageInvalid(): void
    {
        $result = StatusMessage::getMessage('non_existent_key');
        $this->assertFalse($result);
    }

    /**
     * Test getStatus returns correct HTTP status code
     *
     * @return void
     */
    public function testGetStatus(): void
    {
        $this->assertEquals(400, StatusMessage::getStatus('missing_field'));
        $this->assertEquals(401, StatusMessage::getStatus('unauthorized'));
        $this->assertEquals(500, StatusMessage::getStatus('missing_entity'));
        $this->assertFalse(StatusMessage::getStatus('non_existent'));
    }

    /**
     * Test getResponseText returns correct text
     *
     * @return void
     */
    public function testGetResponseText(): void
    {
        $this->assertEquals('Missing Field!', StatusMessage::getResponseText('missing_field'));
        $this->assertEquals('Unauthorized Access!', StatusMessage::getResponseText('unauthorized'));
        $this->assertFalse(StatusMessage::getResponseText('non_existent'));
    }

    /**
     * Test getCode returns correct code string
     *
     * @return void
     */
    public function testGetCode(): void
    {
        $this->assertEquals('A100', StatusMessage::getCode('missing_field'));
        $this->assertEquals('B100', StatusMessage::getCode('unauthorized'));
        $this->assertEquals('FILESERVICE-1', StatusMessage::getCode('file_service_missing_tmp_file'));
        $this->assertFalse(StatusMessage::getCode('non_existent'));
    }

    /**
     * Test getType returns correct message type
     *
     * @return void
     */
    public function testGetType(): void
    {
        $this->assertEquals('error', StatusMessage::getType('missing_field'));
        $this->assertFalse(StatusMessage::getType('non_existent'));
    }

    /**
     * Test toString produces formatted output
     *
     * @return void
     */
    public function testToString(): void
    {
        $result = StatusMessage::toString('missing_field');

        $this->assertStringContainsString('Missing Field!', $result);
        $this->assertStringContainsString('ERROR', $result);
        $this->assertStringContainsString('A100', $result);
    }
}
