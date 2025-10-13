<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'harga',
        'status',
        'link',
        'persentasi',
        'user_id'
    ];

    public function histories()
    {
        return $this->hasMany(TargetHistory::class);
    }

       public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
