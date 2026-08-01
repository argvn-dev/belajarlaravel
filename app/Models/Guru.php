<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'mapel'])]
class Guru extends Model
{
    protected $table = 'guru';

    public function waliKelas(): HasMany
    {
        return $this->hasMany(WaliKelas::class);
    }
}
