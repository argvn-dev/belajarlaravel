<?php

use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kehadiran;
use App\Models\KehadiranSiswa;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('kehadiran index lists attendance with counts', function () {
    $this->actingAs(User::factory()->create());

    $kelas = Kelas::query()->create(['nama' => 'X IPA 1', 'jurusan' => 'IPA', 'tingkat' => 'X']);
    $guru = Guru::query()->create(['nama' => 'Budi Santoso', 'mapel' => 'Matematika']);
    $jadwal = JadwalPelajaran::query()->create([
        'tahun_ajaran_id' => TahunAjaran::query()->create(['nama' => '2026/2027', 'aktif' => true])->id,
        'kelas_id' => $kelas->id,
        'guru_id' => $guru->id,
        'hari' => 'Senin',
        'jam_mulai' => '07:00',
        'jam_selesai' => '08:30',
    ]);

    $kehadiran = Kehadiran::query()->create([
        'tanggal' => '2026-07-01',
        'jadwal_pelajaran_id' => $jadwal->id,
        'dicatat_oleh' => User::query()->first()->id,
    ]);

    $siswa = Siswa::query()->create(['nis' => '12345', 'nama' => 'Ani', 'status_aktif' => true]);
    KehadiranSiswa::query()->create(['kehadiran_id' => $kehadiran->id, 'siswa_id' => $siswa->id, 'status' => 'Hadir']);
    $siswa2 = Siswa::query()->create(['nis' => '67890', 'nama' => 'Budi', 'status_aktif' => true]);
    KehadiranSiswa::query()->create(['kehadiran_id' => $kehadiran->id, 'siswa_id' => $siswa2->id, 'status' => 'Alpa']);

    $this->get(route('kehadiran.index'))->assertOk();

    $this->get(route('kehadiran.edit', $kehadiran))->assertOk();

    $this->put(route('kehadiran.update', $kehadiran), [
        'tanggal' => '2026-07-02',
        'keterangan' => 'Masuk pengganti',
        'statuses' => [
            ['id' => $kehadiran->kehadiranSiswa()->first()->id, 'status' => 'Sakit'],
            ['id' => $kehadiran->kehadiranSiswa()->orderByDesc('id')->first()->id, 'status' => 'Izin'],
        ],
    ])->assertRedirect(route('kehadiran.index'));

    expect(Kehadiran::find($kehadiran->id)->tanggal->toDateString())->toBe('2026-07-02');
    $this->assertDatabaseHas('kehadiran', ['id' => $kehadiran->id, 'keterangan' => 'Masuk pengganti']);
    $this->assertDatabaseHas('kehadiran_siswa', ['kehadiran_id' => $kehadiran->id, 'status' => 'Sakit']);
    $this->assertDatabaseHas('kehadiran_siswa', ['kehadiran_id' => $kehadiran->id, 'status' => 'Izin']);

    $this->delete(route('kehadiran.destroy', $kehadiran))->assertRedirect(route('kehadiran.index'));

    $this->assertDatabaseMissing('kehadiran', ['id' => $kehadiran->id]);
    $this->assertDatabaseMissing('kehadiran_siswa', ['kehadiran_id' => $kehadiran->id]);
});
