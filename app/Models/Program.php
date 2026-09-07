<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'title',
        'supplier',
        'npwp',
        'category',
        'invoice_no',
        'dpp_amount',
        'ppn_amount',
        'total_amount',
        'due_date',
        'status'
    ];

    protected $casts = [
        'dpp_amount' => 'float',
        'ppn_amount' => 'float',
        'total_amount' => 'float',
        'due_date' => 'date'
    ];

    public function documents()
    {
        return $this->hasMany(ProgramDocument::class, 'program_id');
    }
}
