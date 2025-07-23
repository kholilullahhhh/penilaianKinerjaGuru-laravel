<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penilaian_kinerja extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'indikator_id',
        'skor_akhir',
        'kategori',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function indikator()
    {
        return $this->belongsTo(Indicators::class, 'indikator_id', 'id');
    }


}
