<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSkuAttribute extends Model
{
    protected $fillable = [
        'name','values'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
