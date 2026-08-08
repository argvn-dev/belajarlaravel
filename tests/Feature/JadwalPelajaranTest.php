<?php

use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated users can manage jadwal pelajaran', function () {
    $this->actingAs(User::factory()->create());

    $tahunAjaran = TahunAjaran::query()->create(['nama' => '2026/2027', 'aktif' => true]);
    $kelas = Kelas::query()->create(['nama' => 'X IPA 1', 'jurusan' => 'IPA', 'tingkat' => 'X']);
    $guru = Guru::query()->create(['nama' => 'Budi Santoso', 'mapel' => 'Matematika']);

    $this->post(route('jadwal-pelajaran.store'), [
        'tahun_ajaran_id' => $tahunAjaran->id,
        'kelas_id' => $kelas->id,
        'guru_id' => $guru->id,
        'hari' => 'Senin',
        'jam_mulai' => '07:00',
        'jam_selesai' => '08:30',
    ])->assertRedirect(route('jadwal-pelajaran.index'));

    $jadwal = JadwalPelajaran::query()->firstOrFail();

    $this->put(route('jadwal-pelajaran.update', $jadwal), [
        'tahun_ajaran_id' => $tahunAjaran->id,
        'kelas_id' => $kelas->id,
        'guru_id' => $guru->id,
        'hari' => 'Selasa',
        'jam_mulai' => '08:00',
        'jam_selesai' => '09:30',
    ])->assertRedirect(route('jadwal-pelajaran.index'));

    $this->assertDatabaseHas('jadwal_pelajaran', [
        'id' => $jadwal->id,
        'hari' => 'Selasa',
        'jam_mulai' => '08:00',
        'jam_selesai' => '09:30',
    ]);

    $this->delete(route('jadwal-pelajaran.destroy', $jadwal))->assertRedirect(route('jadwal-pelajaran.index'));

    $this->assertDatabaseMissing('jadwal_pelajaran', ['id' => $jadwal->id]);
});

test('jadwal pelajaran requires the end time to be after the start time', function () {
    $this->actingAs(User::factory()->create());

    $tahunAjaran = TahunAjaran::query()->create(['nama' => '2026/2027']);
    $kelas = Kelas::query()->create(['nama' => 'X IPA 1', 'jurusan' => 'IPA', 'tingkat' => 'X']);
    $guru = Guru::query()->create(['nama' => 'Budi Santoso']);

    $this->from(route('jadwal-pelajaran.index'))->post(route('jadwal-pelajaran.store'), [
        'tahun_ajaran_id' => $tahunAjaran->id,
        'kelas_id' => $kelas->id,
        'guru_id' => $guru->id,
        'hari' => 'Senin',
        'jam_mulai' => '08:00',
        'jam_selesai' => '08:00',
    ])->assertRedirect(route('jadwal-pelajaran.index'))
        ->assertSessionHasErrors('jam_selesai');
});
