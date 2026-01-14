<?php
declare(strict_types=1);

namespace ButterCream\Test\TestCase\Controller\Component;

use ButterCream\Controller\Component\RefererComponent;
use Cake\TestSuite\TestCase;

/**
 * ButterCream\Controller\Component\RefererComponent Test Case
 */
class RefererComponentTest extends TestCase
{
    /**
     * Test that component class exists
     *
     * @return void
     */
    public function testComponentExists(): void
    {
        $this->assertTrue(class_exists(RefererComponent::class));
    }

    /**
     * Test that component has expected methods
     *
     * @return void
     */
    public function testComponentHasMethods(): void
    {
        $this->assertTrue(method_exists(RefererComponent::class, 'normalizeUrl'));
        $this->assertTrue(method_exists(RefererComponent::class, 'getReferer'));
        $this->assertTrue(method_exists(RefererComponent::class, 'setReferer'));
        $this->assertTrue(method_exists(RefererComponent::class, 'redirect'));
        $this->assertTrue(method_exists(RefererComponent::class, 'isMatch'));
        $this->assertTrue(method_exists(RefererComponent::class, 'ignore'));
    }
}
