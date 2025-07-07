<?php

namespace Tests\Unit;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }

    /**
     * Test basic arithmetic.
     */
    public function test_basic_arithmetic(): void
    {
        $result = 2 + 2;
        $this->assertEquals(4, $result);
    }

    /**
     * Test string operations.
     */
    public function test_string_operations(): void
    {
        $string = 'Hello World';
        $this->assertStringContainsString('World', $string);
        $this->assertEquals(11, strlen($string));
    }
}
