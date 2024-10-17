<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnrollModel extends Model
{
    use HasFactory;
    protected $table = 'enroll';
    protected $fillable = [
        'nama_filter',
        'machine',
        'IdUser'
    ];
}
