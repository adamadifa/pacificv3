<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Targetkomisi extends Model
{
    use HasFactory;
    protected $table = 'komisi_target';
    protected $guarded = [];
}
