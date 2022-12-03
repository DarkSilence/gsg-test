<?php

namespace App\Http\Controllers;

use App\Exceptions\VoucherIsExpired;
use App\Exceptions\VoucherIsNotFound;
use App\Exceptions\VoucherIsUsed;
use App\Http\Requests\StoreOrder;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Voucher;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected const pageSize = 10;

    /**
     * Display a listing of the resource.
     *
     * @return OrderCollection
     */
    public function index(): OrderCollection
    {
        return new OrderCollection(
            Order::with('voucher')->orderBy('id')->cursorPaginate(self::pageSize)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreOrder $request
     * @return OrderResource
     * @throws Exception
     */
    public function store(StoreOrder $request)
    {
        $validated = $request->validated();
        if ($request->voucher_code) {
            $voucher = Voucher::firstWhere('unique_code', $request->voucher_code);

            if (!($voucher instanceof Voucher)) {
                throw new VoucherIsNotFound();
            }
            if ($voucher->isExpired()) {
                throw new VoucherIsExpired();
            }
            if ($voucher->isUsed()) {
                throw new VoucherIsUsed();
            }
        }

        DB::beginTransaction();

        try {
            $order = new Order($validated);

            if (isset($voucher)) {
                $voucher->order()->save($order);
                $voucher->update(
                    [
                        'used_dt' => Carbon::now(),
                    ]
                );
                if ($voucher->amount < $order->total_amount) {
                    $order->amount_to_pay = $order->total_amount - $voucher->amount;
                } else {
                    $order->amount_to_pay = 0;
                }
            } else {
                $order->amount_to_pay = $order->total_amount;
            }

            $order->save();

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return new OrderResource($order);
    }
}
