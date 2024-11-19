<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportData extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'alternate_phone',
        'gender',
        'dob',
        'reg_number',
        'address'  
    ];

    protected $casts = [
        'dob' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
