<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['tanggal', 'jadwal_pelajaran_id', 'keterangan', 'dicatat_oleh'])]
class Kehadiran extends Model
{
    protected $table = 'kehadiran';

    protected function casts(): array
    {
        return ['tanggal' => 'date'];
    }

    public function jadwalPelajaran(): BelongsTo
    {
        return $this->belongsTo(JadwalPelajaran::class);
    }

    public function dicatatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public function kehadiranSiswa(): HasMany
    {
        return $this->hasMany(KehadiranSiswa::class);
    }
}
