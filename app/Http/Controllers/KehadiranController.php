<?php

namespace App\Http\Controllers;

use App\Models\JadwalPelajaran;
use App\Models\Kehadiran;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class KehadiranController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Kehadiran/Index', [
            'kehadiran' => Kehadiran::query()
                ->with([
                    'jadwalPelajaran.kelas:id,nama,jurusan,tingkat',
                    'jadwalPelajaran.guru:id,nama,mapel',
                ])
                ->withCount([
                    'kehadiranSiswa as jumlah_hadir' => fn ($query) => $query->where('status', 'Hadir'),
                    'kehadiranSiswa as sakit' => fn ($query) => $query->where('status', 'Sakit'),
                    'kehadiranSiswa as izin' => fn ($query) => $query->where('status', 'Izin'),
                    'kehadiranSiswa as alpa' => fn ($query) => $query->where('status', 'Alpa'),
                ])
                ->orderByDesc('tanggal')
                ->orderByDesc('id')
                ->get(),
        ]);
    }

    public function create(Request $request): Response
    {
        $tahunAjaran = TahunAjaran::query()->where('aktif', true)->first()
            ?? TahunAjaran::query()->latest()->first();

        $kelasId = $request->integer('kelas_id');

        return Inertia::render('Kehadiran/Create', [
            'kelas' => Kelas::query()->orderBy('tingkat')->orderBy('nama')->get(['id', 'nama', 'jurusan', 'tingkat']),
            'tahunAjaran' => $tahunAjaran ? ['id' => $tahunAjaran->id, 'nama' => $tahunAjaran->nama] : null,
            'jadwalPelajaran' => $kelasId && $tahunAjaran
                ? JadwalPelajaran::query()
                    ->where('kelas_id', $kelasId)
                    ->where('tahun_ajaran_id', $tahunAjaran->id)
                    ->with('guru:id,nama,mapel')
                    ->orderBy('hari')
                    ->orderBy('jam_mulai')
                    ->get(['id', 'hari', 'jam_mulai', 'jam_selesai', 'guru_id'])
                : [],
            'siswa' => $kelasId && $tahunAjaran
                ? Siswa::query()
                    ->whereHas('kelasSiswa', fn ($query) => $query->where('kelas_id', $kelasId)->where('tahun_ajaran_id', $tahunAjaran->id))
                    ->orderBy('nama')
                    ->get(['id', 'nis', 'nama'])
                : [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $tahunAjaran = TahunAjaran::query()->where('aktif', true)->first()
            ?? TahunAjaran::query()->latest()->first();

        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'jadwal_pelajaran_id' => [
                'required',
                'integer',
                Rule::exists('jadwal_pelajaran', 'id')->where(fn ($query) => $query->where('tahun_ajaran_id', $tahunAjaran?->id)),
            ],
            'keterangan' => ['nullable', 'string'],
            'statuses' => ['required', 'array'],
            'statuses.*.siswa_id' => ['required', 'integer', 'exists:siswa,id'],
            'statuses.*.status' => ['required', Rule::in(['Hadir', 'Sakit', 'Izin', 'Alpa'])],
        ]);

        $kehadiran = Kehadiran::query()->create([
            'tanggal' => $data['tanggal'],
            'jadwal_pelajaran_id' => $data['jadwal_pelajaran_id'],
            'keterangan' => $data['keterangan'],
            'dicatat_oleh' => $request->user()->id,
        ]);

        $kehadiran->kehadiranSiswa()->createMany(
            collect($data['statuses'])->map(fn ($item) => [
                'siswa_id' => $item['siswa_id'],
                'status' => $item['status'],
            ])->all()
        );

        return to_route('kehadiran.index');
    }

    public function edit(Kehadiran $kehadiran, Request $request): Response
    {
        $kehadiran->load('jadwalPelajaran');

        $tahunAjaranId = $kehadiran->jadwalPelajaran->tahun_ajaran_id;
        $kelasId = $request->integer('kelas_id', $kehadiran->jadwalPelajaran->kelas_id);

        $siswaStatus = $kehadiran->kehadiranSiswa()->pluck('status', 'siswa_id');

        return Inertia::render('Kehadiran/Edit', [
            'kehadiran' => [
                'id' => $kehadiran->id,
                'tanggal' => $kehadiran->tanggal->toDateString(),
                'keterangan' => $kehadiran->keterangan,
                'kelas_id' => $kehadiran->jadwalPelajaran->kelas_id,
                'jadwal_pelajaran_id' => $kehadiran->jadwal_pelajaran_id,
            ],
            'statuses' => $siswaStatus,
            'kelas' => Kelas::query()->orderBy('tingkat')->orderBy('nama')->get(['id', 'nama', 'jurusan', 'tingkat']),
            'jadwalPelajaran' => JadwalPelajaran::query()
                ->where('kelas_id', $kelasId)
                ->where('tahun_ajaran_id', $tahunAjaranId)
                ->with('guru:id,nama,mapel')
                ->orderBy('hari')
                ->orderBy('jam_mulai')
                ->get(['id', 'hari', 'jam_mulai', 'jam_selesai', 'guru_id']),
            'siswa' => Siswa::query()
                ->whereHas('kelasSiswa', fn ($query) => $query->where('kelas_id', $kelasId)->where('tahun_ajaran_id', $tahunAjaranId))
                ->orderBy('nama')
                ->get(['id', 'nis', 'nama']),
        ]);
    }

    public function update(Request $request, Kehadiran $kehadiran): RedirectResponse
    {
        $tahunAjaranId = $kehadiran->jadwalPelajaran->tahun_ajaran_id;

        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'jadwal_pelajaran_id' => [
                'required',
                'integer',
                Rule::exists('jadwal_pelajaran', 'id')->where(fn ($query) => $query->where('tahun_ajaran_id', $tahunAjaranId)),
            ],
            'keterangan' => ['nullable', 'string'],
            'statuses' => ['required', 'array'],
            'statuses.*.siswa_id' => ['required', 'integer', 'exists:siswa,id'],
            'statuses.*.status' => ['required', Rule::in(['Hadir', 'Sakit', 'Izin', 'Alpa'])],
        ]);

        $kehadiran->update([
            'tanggal' => $data['tanggal'],
            'jadwal_pelajaran_id' => $data['jadwal_pelajaran_id'],
            'keterangan' => $data['keterangan'],
        ]);

        $kehadiran->kehadiranSiswa()->delete();
        $kehadiran->kehadiranSiswa()->createMany(
            collect($data['statuses'])->map(fn ($item) => [
                'siswa_id' => $item['siswa_id'],
                'status' => $item['status'],
            ])->all()
        );

        return to_route('kehadiran.index');
    }

    public function destroy(Kehadiran $kehadiran): RedirectResponse
    {
        $kehadiran->kehadiranSiswa()->delete();
        $kehadiran->delete();

        return to_route('kehadiran.index');
    }
}
