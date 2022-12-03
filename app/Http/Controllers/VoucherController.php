<?php

namespace App\Http\Controllers;

use App\Exceptions\VoucherIsExpired;
use App\Exceptions\VoucherIsUsed;
use App\Http\Requests\StoreVoucher;
use App\Http\Resources\VoucherCollection;
use App\Http\Resources\VoucherResource;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    protected const pageSize = 10;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return VoucherCollection
     */
    public function index(Request $request)
    {
        if (isset($request->expired)) {
            $where = [
                ['expired_dt', '<=', Carbon::now(),],
            ];
        } else {
            $where = [
                ['used_dt',], // is null
                ['expired_dt', '>', Carbon::now(),],
            ];
        }

        return new VoucherCollection(
            Voucher::where($where)->orderBy('id')->cursorPaginate(self::pageSize)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreVoucher $request
     * @return VoucherResource
     */
    public function store(StoreVoucher $request): VoucherResource
    {
        $validated = $request->validated();
        $voucher = new Voucher($validated);
        $voucher->save();
        return new VoucherResource($voucher);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Voucher $voucher
     * @return VoucherResource
     */
    public function show(Voucher $voucher): VoucherResource
    {
        return new VoucherResource($voucher);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Voucher  $voucher
     * @return VoucherResource
     */
    public function update(StoreVoucher $request, Voucher $voucher)
    {
        if ($voucher->isExpired()) {
            throw new VoucherIsExpired();
        }
        if ($voucher->isUsed()) {
            throw new VoucherIsUsed();
        }
        $validated = $request->validated();
        $voucher->update($validated);
        return new VoucherResource($voucher);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return response()->json(null);
    }
}
