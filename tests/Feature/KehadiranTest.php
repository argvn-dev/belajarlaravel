<?php

use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kehadiran;
use App\Models\KehadiranSiswa;
use App\Models\Kelas;
use App\Models\KelasSiswa;
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
        'jadwal_pelajaran_id' => $kehadiran->jadwal_pelajaran_id,
        'keterangan' => 'Masuk pengganti',
        'statuses' => [
            ['siswa_id' => $siswa->id, 'status' => 'Sakit'],
            ['siswa_id' => $siswa2->id, 'status' => 'Izin'],
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

test('authenticated users can create attendance with dependent jadwal and students', function () {
    $this->actingAs(User::factory()->create());

    $tahunAjaran = TahunAjaran::query()->create(['nama' => '2026/2027', 'aktif' => true]);
    $kelas = Kelas::query()->create(['nama' => 'X IPA 1', 'jurusan' => 'IPA', 'tingkat' => 'X']);
    $guru = Guru::query()->create(['nama' => 'Budi Santoso', 'mapel' => 'Matematika']);
    $jadwal = JadwalPelajaran::query()->create([
        'tahun_ajaran_id' => $tahunAjaran->id,
        'kelas_id' => $kelas->id,
        'guru_id' => $guru->id,
        'hari' => 'Senin',
        'jam_mulai' => '07:00',
        'jam_selesai' => '08:30',
    ]);

    $siswa = Siswa::query()->create(['nis' => '12345', 'nama' => 'Ani', 'status_aktif' => true]);
    KelasSiswa::query()->create(['siswa_id' => $siswa->id, 'kelas_id' => $kelas->id, 'tahun_ajaran_id' => $tahunAjaran->id]);
    $siswa2 = Siswa::query()->create(['nis' => '67890', 'nama' => 'Budi', 'status_aktif' => true]);
    KelasSiswa::query()->create(['siswa_id' => $siswa2->id, 'kelas_id' => $kelas->id, 'tahun_ajaran_id' => $tahunAjaran->id]);

    $this->get(route('kehadiran.create'))->assertOk();

    $this->get(route('kehadiran.create', ['kelas_id' => $kelas->id]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->where('jadwalPelajaran', fn ($list) => count($list) === 1)
            ->where('siswa', fn ($list) => count($list) === 2));

    $this->post(route('kehadiran.store'), [
        'tanggal' => '2026-07-05',
        'jadwal_pelajaran_id' => $jadwal->id,
        'keterangan' => null,
        'statuses' => [
            ['siswa_id' => $siswa->id, 'status' => 'Hadir'],
            ['siswa_id' => $siswa2->id, 'status' => 'Alpa'],
        ],
    ])->assertRedirect(route('kehadiran.index'));

    $kehadiran = Kehadiran::query()->latest()->first();
    $this->assertNotNull($kehadiran);
    $this->assertDatabaseHas('kehadiran_siswa', ['kehadiran_id' => $kehadiran->id, 'siswa_id' => $siswa->id, 'status' => 'Hadir']);
    $this->assertDatabaseHas('kehadiran_siswa', ['kehadiran_id' => $kehadiran->id, 'siswa_id' => $siswa2->id, 'status' => 'Alpa']);
});
