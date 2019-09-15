<?php

namespace App\Transformers;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use League\Fractal\TransformerAbstract;


class UserAddressTransformer extends TransformerAbstract
{
    public function transform (UserAddress $userAddress)
    {
        return [
            'id' => $userAddress->id,
            'province' => $userAddress->province,
            'city' => $userAddress->city,
            'district' => $userAddress->district,
            'address' => $userAddress->address,
            'contact_name' => $userAddress->contact_name,
            'contact_phone' => $userAddress->contact_phone,
        ];
    }
}