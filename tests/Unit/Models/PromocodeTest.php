<?php

namespace Zorb\Promocodes\Tests\Unit\Models;

use Zorb\Promocodes\Tests\TestCase;
use Zorb\Promocodes\Tests\TestUser;
use Zorb\Promocodes\Models\Promocode;
use Zorb\Promocodes\Facades\Promocodes;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PromocodeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_get_expired_records()
    {
        Promocodes::create();
        $record = Promocode::first();

        $this->assertCount(0, Promocode::expired()->get());

        $record->update(['expires_at' => now()->subDay()]);

        $this->assertCount(1, Promocode::expired()->get());
    }

    /** @test */
    function it_can_get_not_expired_records()
    {
        Promocodes::create();
        $record = Promocode::first();

        $this->assertCount(1, Promocode::notExpired()->get());

        $record->update(['expires_at' => now()->subDay()]);

        $this->assertCount(0, Promocode::notExpired()->get());
    }

    /** @test */
    function it_can_get_disposable_records()
    {
        Promocodes::create();
        $record = Promocode::first();

        $this->assertCount(0, Promocode::disposable()->get());

        $record->update(['is_disposable' => true]);

        $this->assertCount(1, Promocode::disposable()->get());
    }

    /** @test */
    function it_can_get_non_disposable_records()
    {
        Promocodes::create();
        $record = Promocode::first();

        $this->assertCount(1, Promocode::nonDisposable()->get());

        $record->update(['is_disposable' => true]);

        $this->assertCount(0, Promocode::nonDisposable()->get());
    }

    /** @test */
    function it_can_get_record_by_code()
    {
        Promocodes::create();
        $record = Promocode::first();

        $this->assertEquals($record, Promocode::byCode($record->code)->first());
    }

    /** @test */
    function it_can_determine_if_code_is_available()
    {
        Promocodes::create();
        $record = Promocode::first();

        $this->assertTrue($record->isAvailable());

        $record->update(['expires_at' => now()->subDay()]);

        $this->assertFalse($record->isAvailable());

        $record->update(['expires_at' => null, 'quantity' => 0]);

        $this->assertFalse($record->isAvailable());
    }

    /** @test */
    function it_can_determine_if_code_has_quantity()
    {
        Promocodes::setQuantity(3)->create();
        $record = Promocode::first();

        $this->assertTrue($record->hasQuantity());
        $this->assertEquals(3, $record->quantity);

        $record->update(['quantity' => null]);

        $this->assertTrue($record->hasQuantity());

        $record->update(['quantity' => 0]);

        $this->assertFalse($record->hasQuantity());
    }

    /** @test */
    function it_can_determine_if_code_is_expired()
    {
        Promocodes::setExpiry(3)->create();
        $record = Promocode::first();

        $this->assertFalse($record->isExpired());
        $this->assertEquals(3, $record->expires_at->diffInDays(now()));

        $record->update(['expires_at' => null]);

        $this->assertFalse($record->isExpired());

        $record->update(['expires_at' => now()->subDay()]);

        $this->assertTrue($record->isExpired());
    }

    /** @test */
    function it_can_determine_if_code_is_disposable()
    {
        Promocodes::setDisposable()->create();
        $record = Promocode::first();

        $this->assertTrue($record->isDisposable());

        $record->update(['is_disposable' => false]);

        $this->assertFalse($record->isDisposable());
    }

    /** @test */
    function it_can_determine_if_code_requires_auth()
    {
        Promocodes::setAuthRequired()->create();
        $record = Promocode::first();

        $this->assertTrue($record->authRequired());

        $record->update(['auth_required' => false]);

        $this->assertFalse($record->authRequired());
    }

    /** @test */
    function it_has_users_relation()
    {
        config(['promocodes.database.user_model' => TestUser::class]);

        Promocodes::create();
        $record = Promocode::first();
        $user = TestUser::create(['name' => 'John Doe']);

        $this->assertCount(0, $record->users);

        $record->users()->attach($user->id, ['used_at' => now()]);

        $this->assertCount(1, $record->fresh()->users);
    }

    /** @test */
    function it_can_use_different_table_name()
    {
        config(['promocodes.database.promocodes_table' => 'coupons']);

        $this->createCouponsTable();

        $codes = Promocodes::create();

        $this->assertDatabaseHas('coupons', [
            'code' => $codes->first()->code,
        ]);
    }

    /** @test */
    function it_can_use_different_table_name_for_relation()
    {
        config([
            'promocodes.database.promocodes_table' => 'coupons',
            'promocodes.database.pivot_table' => 'coupon_user',
            'promocodes.database.foreign_pivot_key' => 'coupon_id',
            'promocodes.database.user_model' => TestUser::class,
        ]);

        $this->createCouponsTable();
        $this->createCouponUserTable();

        Promocodes::create();
        $record = Promocode::first();
        $user = TestUser::create(['name' => 'John Doe']);

        $this->assertCount(0, $record->users);

        $record->users()->attach($user->id, ['used_at' => now()]);

        $this->assertCount(1, $record->fresh()->users);
    }
}
