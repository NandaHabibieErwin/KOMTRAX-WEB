<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcelModel extends Model
{
    use HasFactory;
    protected $table = 'excel';
    protected $fillable = ['filename', 'filepath'];
}
