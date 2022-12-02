<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabaRugiBulanan extends Model
{
    use HasFactory;
    protected $table = 'laba_rugi_bulanan';
    protected $fillable = ['nomor', 'account', 'balance', 'class', 'created_at'];
}
