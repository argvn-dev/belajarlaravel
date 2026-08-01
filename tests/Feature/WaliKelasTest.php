<?php

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Models\WaliKelas;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated users can assign and remove wali kelas', function () {
    $this->actingAs(User::factory()->create());
    $guru = Guru::query()->create(['nama' => 'Budi']);
    $kelas = Kelas::query()->create(['nama' => 'X IPA 1', 'jurusan' => 'IPA', 'tingkat' => 'X']);
    $tahunAjaran = TahunAjaran::query()->create(['nama' => '2025/2026']);

    $this->post(route('wali-kelas.store'), [
        'guru_id' => $guru->id,
        'kelas_id' => $kelas->id,
        'tahun_ajaran_id' => $tahunAjaran->id,
    ])->assertRedirect(route('guru.index'));

    $waliKelas = WaliKelas::query()->firstOrFail();

    $this->assertDatabaseHas('wali_kelas', ['id' => $waliKelas->id, 'guru_id' => $guru->id]);

    $this->delete(route('wali-kelas.destroy', $waliKelas))->assertRedirect(route('guru.index'));

    $this->assertDatabaseMissing('wali_kelas', ['id' => $waliKelas->id]);
});
