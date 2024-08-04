<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patients';

    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'suffix_name',
        'date_of_birth',
        'address',
    ];

    // Define relationships if necessary
    // For example, if Patient has many Admissions:
    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }
}
