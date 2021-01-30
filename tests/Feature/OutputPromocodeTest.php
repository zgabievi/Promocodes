<?php

namespace Zorb\Promocodes\Tests\Feature;

use Illuminate\Support\Collection;
use Zorb\Promocodes\Tests\TestCase;
use Zorb\Promocodes\Facades\Promocodes;

class OutputPromocodeTest extends TestCase
{
    /** @test */
    function it_outputs_code()
    {
        $codes = Promocodes::output();

        $this->assertInstanceOf(Collection::class, $codes);
        $this->assertCount(1, $codes);
    }

    /** @test */
    function it_outputs_multiple_codes()
    {
        $codes = Promocodes::setAmount(10)
            ->output();

        $this->assertCount(10, $codes);
    }

    /** @test */
    function it_outputs_multiple_unique_codes()
    {
        $codes = Promocodes::setAmount(10)
            ->output();

        $this->assertCount(10, $codes);
        $this->assertCount(10, $codes->unique());
    }
}
