<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mutasikendaraan extends Model
{
    use HasFactory;
    protected $table = 'kendaraan_mutasi';
    protected $guarded = [];
}
