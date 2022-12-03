<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $array = parent::toArray($request);
        if (isset($array['voucher'])) {
            $array['voucher_code'] = $array['voucher']['unique_code'];
            unset($array['voucher']);
        }
        unset($array['voucher_id']);
        return $array;
    }
}
