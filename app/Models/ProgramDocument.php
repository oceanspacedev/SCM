<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramDocument extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'program_id',
        'type',
        'file_name',
        'file_size',
        'file_path',
        'uploaded_at'
    ];

    protected $casts = [
        'uploaded_at' => 'datetime'
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
}
