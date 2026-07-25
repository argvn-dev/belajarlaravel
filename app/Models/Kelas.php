<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'jurusan', 'tingkat'])]
class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = ['nama', 'jurusan', 'tingkat'];
}
