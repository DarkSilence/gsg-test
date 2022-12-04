<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class VoucherTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Voucher Store
     *
     * @return void
     */
    public function test_voucher_store()
    {
        $expired_dt = Carbon::tomorrow();
        $data = [
            "unique_code" => "BIG30",
            "amount" => 3000,
            "expired_dt" => $expired_dt->toDateTimeString(),
        ];

        // Insert valid voucher
        $response = $this->postJson('/api/vouchers', $data);

        $response
            ->assertStatus(201)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data.id')
                     ->where('data.unique_code', $data['unique_code'])
                     ->where('data.amount', $data['amount'])
                     ->where('data.expired_dt', $expired_dt->jsonSerialize())
                     ->etc()
            );

        // Truing to insert it again
        $response = $this->postJson('/api/vouchers', $data);
        $response
            ->assertStatus(422)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('errors')->etc()
            );

        // Truing to insert with no data
        $response = $this->postJson('/api/vouchers', []);
        $response
            ->assertStatus(422)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('errors')->etc()
            );

        // Truing to insert with partial data
        $response = $this->postJson('/api/vouchers', [
            "unique_code" => "BIG30",
            "amount" => 3000,
        ]);
        $response
            ->assertStatus(422)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('errors')->etc()
            );

        $response = $this->postJson('/api/vouchers', [
            "unique_code" => "BIG30",
            "expired_dt" => $expired_dt->toDateTimeString(),
        ]);
        $response
            ->assertStatus(422)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('errors')->etc()
            );

        $response = $this->postJson('/api/vouchers', [
            "amount" => 3000,
            "expired_dt" => $expired_dt->toDateTimeString(),
        ]);
        $response
            ->assertStatus(422)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('errors')->etc()
            );

        // Wrong data
        $response = $this->postJson('/api/vouchers', [
            "unique_code" => "",
            "amount" => -100,
            "expired_dt" => $expired_dt->toDateTimeString(),
        ]);
        $response
            ->assertStatus(422)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('errors')->etc()
            );
    }

    /**
     * Voucher Remove
     *
     * @return void
     */
    public function test_voucher_remove()
    {
        $expired_dt = Carbon::tomorrow();
        $data = [
            "unique_code" => "BIG30",
            "amount" => 3000,
            "expired_dt" => $expired_dt->toDateTimeString(),
        ];

        // Insert valid voucher
        $response = $this->postJson('/api/vouchers', $data);

        $response
            ->assertStatus(201);

        // Remove
        $response = $this->deleteJson('/api/vouchers/'. $response->json('data.id'), []);
        $response
            ->assertStatus(200);

        // Missing
        $response = $this->deleteJson('/api/vouchers/'. $response->json('data.id'), []);
        $response
            ->assertStatus(405);
    }

    /**
     * Voucher Edit
     *
     * @return void
     */
    public function test_voucher_edit()
    {
        $expired_dt = Carbon::tomorrow();
        $data = [
            "unique_code" => "BIG30",
            "amount" => 3000,
            "expired_dt" => $expired_dt->toDateTimeString(),
        ];

        // Insert valid voucher
        $response = $this->postJson('/api/vouchers', [
            "unique_code" => "BIG40",
            "amount" => 3000,
            "expired_dt" => $expired_dt->toDateTimeString(),
        ]);

        // and one more
        $response = $this->postJson('/api/vouchers', $data);

        $response
            ->assertStatus(201)
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.id', $response->json('data.id'))
                     ->where('data.unique_code', $data['unique_code'])
                     ->where('data.amount', $data['amount'])
                     ->where('data.expired_dt', $expired_dt->jsonSerialize())
                     ->etc()
            );

        $voucherId = $response->json('data.id');

        // Changing smth
        $data['amount'] = 4000;

        $response = $this->putJson('/api/vouchers/'. $voucherId, $data);
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.id', $voucherId)
                     ->where('data.unique_code', $data['unique_code'])
                     ->where('data.amount', $data['amount'])
                     ->where('data.expired_dt', $expired_dt->jsonSerialize())
                     ->etc()
            );

        // Changing code
        $data['unique_code'] = 'BIG50';

        $response = $this->putJson('/api/vouchers/'. $voucherId, $data);
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.id', $voucherId)
                     ->where('data.unique_code', $data['unique_code'])
                     ->where('data.amount', $data['amount'])
                     ->where('data.expired_dt', $expired_dt->jsonSerialize())
                     ->etc()
            );

        // Changing code for existing
        $data['unique_code'] = 'BIG40';

        $response = $this->putJson('/api/vouchers/'. $voucherId, $data);
        $response
            ->assertStatus(422);

        // Changing used
        $data['unique_code'] = 'BIG30';
        $data['used_dt'] = Carbon::now()->toDateTimeString();

        $response = $this->putJson('/api/vouchers/'. $voucherId, $data);
        $response
            ->assertStatus(200);

        $response = $this->putJson('/api/vouchers/'. $voucherId, $data);
        $response
            ->assertStatus(400);


    }
}
