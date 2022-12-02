<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Biaya extends Model
{
    use HasFactory;
    protected $table = 'detail_biaya';

    public function nama()
    {
        return $this->hasOne(KategoriBiaya::class, 'id', 'kategori_biaya');
    }
}
