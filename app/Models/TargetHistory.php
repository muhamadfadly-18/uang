<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'target_id',
        'nilai_tercapai',
        'keterangan',
    ];

    public function target()
    {
        return $this->belongsTo(Target::class);
    }
}
