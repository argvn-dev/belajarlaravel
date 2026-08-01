<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nis', 'nama', 'status_aktif'])]
class Siswa extends Model
{
    protected $table = 'siswa';

    protected function casts(): array
    {
        return ['status_aktif' => 'boolean'];
    }

    public function kelasSiswa(): HasMany
    {
        return $this->hasMany(KelasSiswa::class);
    }
}
