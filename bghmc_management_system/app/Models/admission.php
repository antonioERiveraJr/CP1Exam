<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    use HasFactory;

    // Specify the table name if it's not the plural form of the model name
    protected $table = 'admissions';

    // Specify which attributes can be mass-assigned
    protected $fillable = [
        'patient_id',
        'ward',
        'datetime_of_admission',
        'datetime_of_discharge',
    ];

    // Define relationships if necessary
    // For example, if Admission belongs to Patient:
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
