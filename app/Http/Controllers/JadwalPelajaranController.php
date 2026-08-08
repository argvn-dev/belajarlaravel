<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class JadwalPelajaranController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('JadwalPelajaran/Index', [
            'jadwalPelajaran' => JadwalPelajaran::query()
                ->with(['tahunAjaran:id,nama', 'kelas:id,nama,jurusan,tingkat', 'guru:id,nama,mapel'])
                ->orderBy('hari')
                ->orderBy('jam_mulai')
                ->get(),
            'tahunAjaran' => TahunAjaran::query()->orderByDesc('aktif')->latest()->get(['id', 'nama']),
            'kelas' => Kelas::query()->orderBy('tingkat')->orderBy('nama')->get(['id', 'nama', 'jurusan', 'tingkat']),
            'guru' => Guru::query()->orderBy('nama')->get(['id', 'nama', 'mapel']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        JadwalPelajaran::query()->create($this->validated($request));

        return to_route('jadwal-pelajaran.index');
    }

    public function update(Request $request, JadwalPelajaran $jadwalPelajaran): RedirectResponse
    {
        $jadwalPelajaran->update($this->validated($request));

        return to_route('jadwal-pelajaran.index');
    }

    public function destroy(JadwalPelajaran $jadwalPelajaran): RedirectResponse
    {
        $jadwalPelajaran->delete();

        return to_route('jadwal-pelajaran.index');
    }

    /**
     * @return array{tahun_ajaran_id: int, kelas_id: int, guru_id: int, hari: string, jam_mulai: string, jam_selesai: string}
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'tahun_ajaran_id' => ['required', 'integer', 'exists:tahun_ajaran,id'],
            'kelas_id' => ['required', 'integer', 'exists:kelas,id'],
            'guru_id' => ['required', 'integer', 'exists:guru,id'],
            'hari' => ['required', Rule::in(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'])],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
        ]);
    }
}
