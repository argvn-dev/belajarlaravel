<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\KelasSiswa;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'ARGVN',
            'email' => 'me@argvn.com',
        ]);

        $tahunAjaran = TahunAjaran::query()->create([
            'nama' => '2026/2027',
            'aktif' => true,
        ]);

        $kelas = collect([
            ['nama' => 'X IPA 1', 'jurusan' => 'IPA', 'tingkat' => 'X'],
            ['nama' => 'X IPA 2', 'jurusan' => 'IPA', 'tingkat' => 'X'],
            ['nama' => 'XI IPA 1', 'jurusan' => 'IPA', 'tingkat' => 'XI'],
            ['nama' => 'XI IPS 1', 'jurusan' => 'IPS', 'tingkat' => 'XI'],
            ['nama' => 'XII IPA 1', 'jurusan' => 'IPA', 'tingkat' => 'XII'],
            ['nama' => 'XII IPS 1', 'jurusan' => 'IPS', 'tingkat' => 'XII'],
        ])->map(fn (array $data): Kelas => Kelas::query()->create($data));

        $guru = collect([
            ['nama' => 'Ahmad Fauzi', 'mapel' => 'Matematika'],
            ['nama' => 'Siti Nurhaliza', 'mapel' => 'Bahasa Indonesia'],
            ['nama' => 'Dewi Lestari', 'mapel' => 'Bahasa Inggris'],
            ['nama' => 'Rizky Pratama', 'mapel' => 'Fisika'],
            ['nama' => 'Maya Sari', 'mapel' => 'Kimia'],
            ['nama' => 'Andi Wijaya', 'mapel' => 'Biologi'],
            ['nama' => 'Nina Kurnia', 'mapel' => 'Sejarah'],
            ['nama' => 'Bambang Setiawan', 'mapel' => 'Pendidikan Jasmani'],
        ])->map(fn (array $data): Guru => Guru::query()->create($data));

        collect([
            'Aditya Pranata', 'Aisyah Putri', 'Bagas Ramadhan', 'Citra Lestari',
            'Dimas Saputra', 'Eka Wulandari', 'Farhan Akbar', 'Gita Permata',
            'Hendra Kurniawan', 'Intan Safitri', 'Joko Susilo', 'Kartika Sari',
            'Lukman Hakim', 'Melati Kusuma', 'Naufal Fikri', 'Oktavia Putri',
            'Putra Mahendra', 'Qori Aulia', 'Rafi Maulana', 'Salsa Anindita',
            'Tegar Prakoso', 'Ulfa Rahma', 'Vina Amelia', 'Yusuf Alfarizi',
        ])->each(function (string $nama, int $index) use ($kelas, $tahunAjaran): void {
            $siswa = Siswa::query()->create([
                'nis' => '2026'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                'nama' => $nama,
                'status_aktif' => true,
            ]);

            KelasSiswa::query()->create([
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kelas_id' => $kelas[$index % $kelas->count()]->id,
                'siswa_id' => $siswa->id,
            ]);
        });

        $jadwal = [
            ['hari' => 'Senin', 'jam_mulai' => '07:00', 'jam_selesai' => '08:30', 'guru' => 0],
            ['hari' => 'Selasa', 'jam_mulai' => '07:00', 'jam_selesai' => '08:30', 'guru' => 1],
            ['hari' => 'Rabu', 'jam_mulai' => '08:45', 'jam_selesai' => '10:15', 'guru' => 2],
            ['hari' => 'Kamis', 'jam_mulai' => '10:30', 'jam_selesai' => '12:00', 'guru' => 3],
            ['hari' => 'Jumat', 'jam_mulai' => '07:00', 'jam_selesai' => '08:30', 'guru' => 4],
        ];

        $kelas->each(function (Kelas $item, int $index) use ($jadwal, $guru, $tahunAjaran): void {
            foreach ($jadwal as $slot) {
                JadwalPelajaran::query()->create([
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'kelas_id' => $item->id,
                    'guru_id' => $guru[($slot['guru'] + $index) % $guru->count()]->id,
                    'hari' => $slot['hari'],
                    'jam_mulai' => $slot['jam_mulai'],
                    'jam_selesai' => $slot['jam_selesai'],
                ]);
            }
        });
    }
}
