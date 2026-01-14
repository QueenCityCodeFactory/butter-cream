<?php
declare(strict_types=1);

namespace ButterCream\Test\TestCase\Controller\Component;

use ButterCream\Controller\Component\FlashComponent;
use Cake\TestSuite\TestCase;

/**
 * ButterCream\Controller\Component\FlashComponent Test Case
 */
class FlashComponentTest extends TestCase
{
    /**
     * Test that component exists and can be loaded
     *
     * @return void
     */
    public function testComponentExists(): void
    {
        $this->assertTrue(class_exists(FlashComponent::class));
    }
}
