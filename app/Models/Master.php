<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Master extends Model
{
    protected $fillable = [
        'fans','integral','percent','signboard','layout'
    ];

    // 与user关联
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
