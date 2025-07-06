<?php

namespace Models;

use App\Enums\Protocol;
use Tests\TestCase;

class ProtocolEnumTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_has_correct_cases()
    {
        $this->assertEquals('http', Protocol::HTTP->value);
        $this->assertEquals('https', Protocol::HTTPS->value);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_be_cast_to_string()
    {
        $this->assertEquals('http', Protocol::HTTP->value);
        $this->assertEquals('https', Protocol::HTTPS->value);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_be_compared()
    {
        $this->assertTrue(Protocol::HTTP === Protocol::HTTP);
        $this->assertTrue(Protocol::HTTPS === Protocol::HTTPS);
        $this->assertFalse(Protocol::HTTP === Protocol::HTTPS);
    }
}
