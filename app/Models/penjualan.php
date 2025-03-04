<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class penjualan extends Model
{
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function detailPenjualans()
    {
        return $this->hasMany(Detailpenjualan::class);
    }

}
