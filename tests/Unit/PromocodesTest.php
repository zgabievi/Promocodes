<?php

namespace Zorb\Promocodes\Tests\Unit;

use Illuminate\Support\Collection;
use Zorb\Promocodes\Tests\TestCase;
use Zorb\Promocodes\Models\Promocode;
use Zorb\Promocodes\Facades\Promocodes;

class PromocodesTest extends TestCase
{
    /** @test */
    function it_can_set_custom_prefix()
    {
        $code = Promocodes::setPrefix('ABC');

        $this->assertEquals('ABC', $code->getPrefix());
    }

    /** @test */
    function it_can_set_custom_suffix()
    {
        $code = Promocodes::setSuffix('XYZ');

        $this->assertEquals('XYZ', $code->getSuffix());
    }

    /** @test */
    function it_can_set_custom_amount()
    {
        $code = Promocodes::setAmount(5);

        $this->assertEquals(5, $code->getAmount());
    }

    /** @test */
    function it_can_set_custom_data()
    {
        $code = Promocodes::setData(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $code->getData());
    }

    /** @test */
    function it_can_set_custom_expires_in()
    {
        $code = Promocodes::setExpiry(3);

        $this->assertEquals(3, $code->getExpiry());
    }

    /** @test */
    function it_can_set_custom_quantity()
    {
        $code = Promocodes::setQuantity(10);

        $this->assertEquals(10, $code->getQuantity());
    }

    /** @test */
    function it_can_set_custom_auth_required()
    {
        $code = Promocodes::setAuthRequired();

        $this->assertTrue($code->getAuthRequired());
    }

    /** @test */
    function it_can_set_custom_disposable()
    {
        $code = Promocodes::setDisposable();

        $this->assertTrue($code->getDisposable());
    }

    /** @test */
    function it_can_set_custom_characters()
    {
        $code = Promocodes::setCharacters('ABC012');

        $this->assertEquals('ABC012', $code->getCharacters());
    }

    /** @test */
    function it_can_set_custom_mask()
    {
        $code = Promocodes::setMask('*****');

        $this->assertEquals('*****', $code->getMask());
    }

    /** @test */
    function it_can_set_custom_delimiter()
    {
        $code = Promocodes::setDelimiter('D');

        $this->assertEquals('D', $code->getDelimiter());
    }

    /** @test */
    function it_can_create_codes()
    {
        Promocodes::setAmount(3)->create([
            'foo' => 'bar',
        ]);
        $record = Promocode::first();

        $this->assertCount(3, Promocode::all());
        $this->assertEquals(['foo' => 'bar'], $record->data);
    }

    /** @test */
    function it_can_output_codes()
    {
        $codes = Promocodes::setAmount(3)->output();

        $this->assertCount(3, $codes);
        $this->assertInstanceOf(Collection::class, $codes);
    }

    /** @test */
    function it_can_generate_code()
    {
        $code = Promocodes::generate();

        $this->assertIsString($code);
        $this->assertEquals(9, strlen($code));
    }

    /** @test */
    function it_can_validate_code()
    {
        $codes = collect(['ABC-XYZ', 'FOO-BAR']);

        $this->assertFalse(Promocodes::validate($codes, 'ABC-XYZ'));
        $this->assertTrue(Promocodes::validate($codes, 'DEF-JHI'));
    }

    // use
    // redeem
    // apply
    // available
    // dispose
    // disable
    // expire
    // clear
    // allAvailable
}
