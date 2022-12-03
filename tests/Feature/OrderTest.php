<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

use function sleep;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Order Store
     *
     * @return void
     */
    public function test_order_store()
    {
        $expired_dt = Carbon::tomorrow();

        // Insert vouchers
        $response = $this->postJson('/api/vouchers', [
            "unique_code" => "BIG30",
            "amount" => 3000,
            "expired_dt" => $expired_dt->toDateTimeString(),
        ]);
        $response
            ->assertStatus(201);

        $response = $this->postJson('/api/vouchers', [
            "unique_code" => "BIG40",
            "amount" => 4000,
            "expired_dt" => $expired_dt->toDateTimeString(),
        ]);
        $response
            ->assertStatus(201);

        $response = $this->postJson('/api/vouchers', [
            "unique_code" => "BIG50",
            "amount" => 5000,
            "expired_dt" => $expired_dt->toDateTimeString(),
        ]);
        $response
            ->assertStatus(201);

        // Expired
        $response = $this->postJson('/api/vouchers', [
            "unique_code" => "BIG60",
            "amount" => 6000,
            "expired_dt" => Carbon::now()->addSeconds(5)->toDateTimeString(),
        ]);
        $response
            ->assertStatus(201);


        // Insert orders

        // w/o voucher
        $response = $this->postJson('/api/orders', [
            "total_amount" => 10000,
        ]);
        $response
            ->assertStatus(201)
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.amount_to_pay', 10000)
                     ->where('data.total_amount', 10000)
                     ->etc()
            );

        // 30$
        $response = $this->postJson('/api/orders', [
            "total_amount" => 4000,
            "voucher_code" => "BIG30"
        ]);
        $response
            ->assertStatus(201)
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.amount_to_pay', 1000)
                     ->where('data.total_amount', 4000)
                     ->etc()
            );

        // 40$ equal
        $response = $this->postJson('/api/orders', [
            "total_amount" => 4000,
            "voucher_code" => "BIG40"
        ]);
        $response
            ->assertStatus(201)
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.amount_to_pay', 0)
                     ->where('data.total_amount', 4000)
                     ->etc()
            );

        // 50$ greater than
        $response = $this->postJson('/api/orders', [
            "total_amount" => 4000,
            "voucher_code" => "BIG50"
        ]);
        $response
            ->assertStatus(201)
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.amount_to_pay', 0)
                     ->where('data.total_amount', 4000)
                     ->etc()
            );

        // using twice
        $response = $this->postJson('/api/orders', [
            "total_amount" => 4000,
            "voucher_code" => "BIG30"
        ]);
        $response
            ->assertStatus(400);

        // using expired
        sleep(5);
        $response = $this->postJson('/api/orders', [
            "total_amount" => 4000,
            "voucher_code" => "BIG60"
        ]);
        $response
            ->assertStatus(400);

        // using non existing
        $response = $this->postJson('/api/orders', [
            "total_amount" => 4000,
            "voucher_code" => "VOUCHER"
        ]);
        $response
            ->assertStatus(400);


    }
}
