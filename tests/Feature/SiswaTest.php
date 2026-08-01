<?php

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated users can manage siswa and class placement', function () {
    $this->actingAs(User::factory()->create());
    $kelas = Kelas::query()->create(['nama' => 'X IPA 1', 'jurusan' => 'IPA', 'tingkat' => 'X']);
    $tahunAjaran = TahunAjaran::query()->create(['nama' => '2025/2026', 'aktif' => true]);

    $this->post(route('siswa.store'), [
        'nis' => '12345',
        'nama' => 'Ani',
        'status_aktif' => true,
        'kelas_id' => $kelas->id,
        'tahun_ajaran_id' => $tahunAjaran->id,
    ])->assertRedirect(route('siswa.index'));

    $siswa = Siswa::query()->firstOrFail();

    $this->assertDatabaseHas('kelas_siswa', ['siswa_id' => $siswa->id, 'kelas_id' => $kelas->id]);

    $this->put(route('siswa.update', $siswa), [
        'nis' => '54321',
        'nama' => 'Ani Putri',
        'status_aktif' => false,
        'kelas_id' => $kelas->id,
        'tahun_ajaran_id' => $tahunAjaran->id,
    ])->assertRedirect(route('siswa.index'));

    $this->assertDatabaseHas('siswa', ['id' => $siswa->id, 'nama' => 'Ani Putri', 'status_aktif' => false]);

    $this->delete(route('siswa.destroy', $siswa))->assertRedirect(route('siswa.index'));

    $this->assertDatabaseMissing('siswa', ['id' => $siswa->id]);
    $this->assertDatabaseMissing('kelas_siswa', ['siswa_id' => $siswa->id]);
});
