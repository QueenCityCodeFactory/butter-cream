<?php
declare(strict_types=1);

namespace ButterCream\Test\TestCase\Utility;

use ButterCream\Utility\Muddle;
use Cake\TestSuite\TestCase;

/**
 * ButterCream\Utility\Muddle Test Case
 */
class MuddleTest extends TestCase
{
    /**
     * Test insert method with simple path
     *
     * @return void
     */
    public function testInsertSimplePath(): void
    {
        $array = [];
        $result = Muddle::insert($array, 'key', 'value');
        
        $this->assertTrue($result);
        $this->assertEquals('value', $array['key']);
    }

    /**
     * Test insert method with nested path
     *
     * @return void
     */
    public function testInsertNestedPath(): void
    {
        $array = [];
        $result = Muddle::insert($array, 'level1.level2.level3', 'deep value');
        
        $this->assertTrue($result);
        $this->assertEquals('deep value', $array['level1']['level2']['level3']);
    }

    /**
     * Test insert method with array path
     *
     * @return void
     */
    public function testInsertArrayPath(): void
    {
        $array = [];
        $result = Muddle::insert($array, ['users', '0', 'name'], 'John');
        
        $this->assertTrue($result);
        $this->assertEquals('John', $array['users']['0']['name']);
    }

    /**
     * Test insert method with custom separator
     *
     * @return void
     */
    public function testInsertCustomSeparator(): void
    {
        $array = [];
        $result = Muddle::insert($array, 'level1/level2/level3', 'value', '/');
        
        $this->assertTrue($result);
        $this->assertEquals('value', $array['level1']['level2']['level3']);
    }

    /**
     * Test insert method with empty path returns false
     *
     * @return void
     */
    public function testInsertEmptyPath(): void
    {
        $array = [];
        $result = Muddle::insert($array, [], 'value');
        
        $this->assertFalse($result);
    }

    /**
     * Test insert method overwrites existing values
     *
     * @return void
     */
    public function testInsertOverwritesExisting(): void
    {
        $array = ['key' => 'old value'];
        Muddle::insert($array, 'key', 'new value');
        
        $this->assertEquals('new value', $array['key']);
    }

    /**
     * Test buildDotNotationPath with simple array
     *
     * @return void
     */
    public function testBuildDotNotationPathSimple(): void
    {
        $result = Muddle::buildDotNotationPath(['level1', 'level2', 'level3']);
        
        $this->assertEquals('level1.level2.level3', $result);
    }

    /**
     * Test buildDotNotationPath with prefix
     *
     * @return void
     */
    public function testBuildDotNotationPathWithPrefix(): void
    {
        $result = Muddle::buildDotNotationPath(['level2', 'level3'], 'level1');
        
        $this->assertEquals('level1.level2.level3', $result);
    }

    /**
     * Test buildDotNotationPath with array prefix
     *
     * @return void
     */
    public function testBuildDotNotationPathWithArrayPrefix(): void
    {
        $result = Muddle::buildDotNotationPath(['level3'], ['level1', 'level2']);
        
        $this->assertEquals('level1.level2.level3', $result);
    }

    /**
     * Test buildDotNotationPath with suffix
     *
     * @return void
     */
    public function testBuildDotNotationPathWithSuffix(): void
    {
        $result = Muddle::buildDotNotationPath(['level1', 'level2'], null, 'level3');
        
        $this->assertEquals('level1.level2.level3', $result);
    }

    /**
     * Test buildDotNotationPath with array suffix
     *
     * @return void
     */
    public function testBuildDotNotationPathWithArraySuffix(): void
    {
        $result = Muddle::buildDotNotationPath(['level1'], null, ['level2', 'level3']);
        
        $this->assertEquals('level1.level2.level3', $result);
    }

    /**
     * Test buildDotNotationPath with prefix and suffix
     *
     * @return void
     */
    public function testBuildDotNotationPathWithPrefixAndSuffix(): void
    {
        $result = Muddle::buildDotNotationPath(['level2'], 'level1', 'level3');
        
        $this->assertEquals('level1.level2.level3', $result);
    }

    /**
     * Test buildDotNotationPath with custom separator
     *
     * @return void
     */
    public function testBuildDotNotationPathCustomSeparator(): void
    {
        $result = Muddle::buildDotNotationPath(['level1', 'level2', 'level3'], null, null, '/');
        
        $this->assertEquals('level1/level2/level3', $result);
    }

    /**
     * Test buildDotNotationPath with false prefix/suffix (should ignore)
     *
     * @return void
     */
    public function testBuildDotNotationPathIgnoreFalse(): void
    {
        $result = Muddle::buildDotNotationPath(['level1', 'level2'], false, false);
        
        $this->assertEquals('level1.level2', $result);
    }
}
