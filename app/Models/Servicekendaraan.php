<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicekendaraan extends Model
{
    use HasFactory;
    protected $table = 'kendaraan_service';
    protected $guarded = [];
}
