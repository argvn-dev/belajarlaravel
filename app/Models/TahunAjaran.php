<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'aktif'])]
class TahunAjaran extends Model
{
    protected $table = 'tahun_ajaran';

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
        ];
    }
}
