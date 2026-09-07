<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawImport extends Model
{
    protected $fillable = [
        'file_name',
        'file_key',
        'file_size',
        'file_url',
        'storage_type',
        'imported_rows_count',
        'uploaded_by',
        'notes'
    ];
}
