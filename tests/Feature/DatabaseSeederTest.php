<?php

use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\KelasSiswa;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('database seeder creates school master data and schedules', function () {
    $this->seed();

    expect(TahunAjaran::query()->where('aktif', true)->count())->toBe(1)
        ->and(Kelas::query()->count())->toBe(6)
        ->and(Guru::query()->count())->toBe(8)
        ->and(Siswa::query()->count())->toBe(24)
        ->and(KelasSiswa::query()->count())->toBe(24)
        ->and(JadwalPelajaran::query()->count())->toBe(30);
});
