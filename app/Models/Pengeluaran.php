<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;


    protected $fillable = ['keterangan', 'jumlah', 'harga', 'user_id', 'total'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
