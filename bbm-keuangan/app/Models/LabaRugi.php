<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabaRugi extends Model
{
    use HasFactory;
    protected $table = 'laba_rugi';
    protected $fillable = ['no', 'account', 'balance', 'class', 'created_at'];
}
