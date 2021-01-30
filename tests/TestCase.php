<?php

namespace Zorb\Promocodes\Tests;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Zorb\Promocodes\PromocodesServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    //
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    //
    protected function setUpDatabase()
    {
        Schema::dropIfExists('coupon_user');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('promocode_user');
        Schema::dropIfExists('promocodes');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        include_once __DIR__ . '/../stubs/create_promocodes_table.php.stub';
        (new \CreatePromocodesTable())->up();

        include_once __DIR__ . '/../stubs/create_promocode_user_table.php.stub';
        (new \CreatePromocodeUserTable())->up();
    }

    //
    public function createCouponsTable()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->integer('quantity')->nullable();
            $table->json('data')->nullable();
            $table->boolean('is_disposable')->default(false);
            $table->boolean('auth_required')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    //
    public function createCouponUserTable()
    {
        Schema::create('coupon_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained('coupons')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('used_at');
        });
    }

    //
    protected function getPackageProviders($app)
    {
        return [
            PromocodesServiceProvider::class,
        ];
    }
}
