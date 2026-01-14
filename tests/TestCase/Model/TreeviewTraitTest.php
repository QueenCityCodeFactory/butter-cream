<?php
declare(strict_types=1);

namespace ButterCream\Test\TestCase\Model;

use ButterCream\Model\TreeviewTrait;
use Cake\TestSuite\TestCase;

/**
 * ButterCream\Model\TreeviewTrait Test Case
 */
class TreeviewTraitTest extends TestCase
{
    /**
     * Test that trait exists
     *
     * @return void
     */
    public function testTraitExists(): void
    {
        $this->assertTrue(trait_exists(TreeviewTrait::class));
    }

    /**
     * Test that trait has findTreeview method
     *
     * @return void
     */
    public function testTraitHasFindTreeviewMethod(): void
    {
        $this->assertTrue(method_exists(TreeviewTrait::class, 'findTreeview'));
    }
}
