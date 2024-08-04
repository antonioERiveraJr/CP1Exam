<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    use HasFactory;
 
    protected $table = 'admissions';
 
    protected $fillable = [
        'patient_id',
        'ward',
        'datetime_of_admission',
        'datetime_of_discharge',
    ];
 
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
