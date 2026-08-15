<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['kehadiran_id', 'siswa_id', 'status', 'keterangan'])]
class KehadiranSiswa extends Model
{
    protected $table = 'kehadiran_siswa';

    public function kehadiran(): BelongsTo
    {
        return $this->belongsTo(Kehadiran::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}
