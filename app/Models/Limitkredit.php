<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Limitkredit extends Model
{
    use HasFactory;
    protected $table = 'pengajuan_limitkredit_v3';
    protected $guarded = [];
}
