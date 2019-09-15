<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    protected $fillable = [
        'name','values','prodict_id'
    ];

    public function product(){

        return $this->belongsTo(Product::class);
    }
}
