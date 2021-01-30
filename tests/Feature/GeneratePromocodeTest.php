<?php

namespace Zorb\Promocodes\Tests\Feature;

use Zorb\Promocodes\Tests\TestCase;
use Zorb\Promocodes\Facades\Promocodes;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GeneratePromocodeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_generates_code()
    {
        $code = Promocodes::generate();

        $this->assertNotNull($code);
        $this->assertEquals(9, strlen($code));
    }

    /** @test */
    function it_generates_code_with_prefix()
    {
        $code = Promocodes::setPrefix('ABC')
            ->generate();

        $this->assertNotNull($code);
        $this->assertEquals(13, strlen($code));
        $this->assertStringStartsWith('ABC-', $code);
    }

    /** @test */
    function it_generates_code_with_suffix()
    {
        $code = Promocodes::setSuffix('XYZ')
            ->generate();

        $this->assertNotNull($code);
        $this->assertEquals(13, strlen($code));
        $this->assertStringEndsWith('-XYZ', $code);
    }

    /** @test */
    function it_generates_code_with_delimiter()
    {
        $code = Promocodes::setDelimiter('=')
            ->setPrefix('ABC')
            ->setSuffix('XYZ')
            ->generate();

        $this->assertNotNull($code);
        $this->assertEquals(17, strlen($code));
        $this->assertStringStartsWith('ABC=', $code);
        $this->assertStringEndsWith('=XYZ', $code);
    }

    /** @test */
    function it_generates_code_with_custom_characters()
    {
        $code = Promocodes::setCharacters('ABC')
            ->generate();

        $this->assertNotNull($code);
        $this->assertEquals(preg_match('/[^ABC-]/', $code), 0);
    }

    /** @test */
    function it_generates_code_with_custom_mask()
    {
        $code = Promocodes::setMask('**-**')
            ->generate();

        $this->assertNotNull($code);
        $this->assertEquals(5, strlen($code));
    }
}
