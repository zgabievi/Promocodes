<?php

namespace Zorb\Promocodes\Tests\Feature;

use Zorb\Promocodes\Tests\TestCase;
use Zorb\Promocodes\Models\Promocode;
use Zorb\Promocodes\Facades\Promocodes;

class CreatePromocodeTest extends TestCase
{
    /** @test */
    function it_creates_code()
    {
        $codes = Promocodes::create();

        $this->assertCount(1, $codes);
        $this->assertDatabaseHas('promocodes', [
            'code' => $codes->first()->code,
        ]);
    }

    /** @test */
    function it_creates_multiple_codes()
    {
        $codes = Promocodes::setAmount(3)->create();

        $this->assertCount(3, $codes);

        $this->assertDatabaseHas('promocodes', [
            'code' => $codes->first()->code,
        ]);

        $this->assertDatabaseHas('promocodes', [
            'code' => $codes->last()->code,
        ]);
    }

    /** @test */
    function it_creates_code_with_data()
    {
        Promocodes::setData(['foo' => 'bar'])->create();
        $record = Promocode::first();

        $this->assertEquals('bar', $record->data['foo']);
    }

    /** @test */
    function it_creates_code_with_data_simplified()
    {
        Promocodes::create(['foo' => 'bar']);
        $record = Promocode::first();

        $this->assertEquals('bar', $record->data['foo']);
    }

    /** @test */
    function it_creates_code_with_expiration_date()
    {
        Promocodes::setExpiry(3)->create();
        $record = Promocode::first();

        $this->assertEquals(now()->addDays(3)->day, $record->expires_at->day);
    }

    /** @test */
    function it_creates_disposable_code()
    {
        Promocodes::setDisposable()->create();

        $this->assertDatabaseHas('promocodes', [
            'is_disposable' => true,
        ]);
    }

    /** @test */
    function it_creates_code_with_quantity()
    {
        Promocodes::setQuantity(15)->create();

        $this->assertDatabaseHas('promocodes', [
            'quantity' => 15,
        ]);
    }
}
