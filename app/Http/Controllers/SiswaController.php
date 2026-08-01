<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SiswaController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Siswa/Index', [
            'siswa' => Siswa::query()->with(['kelasSiswa.kelas', 'kelasSiswa.tahunAjaran'])->orderBy('nama')->get(),
            'kelas' => Kelas::query()->orderBy('tingkat')->orderBy('nama')->get(['id', 'nama']),
            'tahunAjaran' => TahunAjaran::query()->orderByDesc('aktif')->latest()->get(['id', 'nama']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $siswa = Siswa::query()->create($data);
        $this->syncKelas($siswa, $data);

        return to_route('siswa.index');
    }

    public function update(Request $request, Siswa $siswa): RedirectResponse
    {
        $data = $this->validated($request);
        $siswa->update($data);
        $this->syncKelas($siswa, $data);

        return to_route('siswa.index');
    }

    public function destroy(Siswa $siswa): RedirectResponse
    {
        $siswa->kelasSiswa()->delete();
        $siswa->delete();

        return to_route('siswa.index');
    }

    /**
     * @return array{nis: string, nama: string, status_aktif: bool, kelas_id: int, tahun_ajaran_id: int}
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'nis' => ['required', 'string', 'max:255'],
            'nama' => ['required', 'string', 'max:255'],
            'status_aktif' => ['required', 'boolean'],
            'kelas_id' => ['required', 'integer', 'exists:kelas,id'],
            'tahun_ajaran_id' => ['required', 'integer', 'exists:tahun_ajaran,id'],
        ]);
    }

    /**
     * @param  array{kelas_id: int, tahun_ajaran_id: int}  $data
     */
    private function syncKelas(Siswa $siswa, array $data): void
    {
        $siswa->kelasSiswa()->delete();
        $siswa->kelasSiswa()->create([
            'kelas_id' => $data['kelas_id'],
            'tahun_ajaran_id' => $data['tahun_ajaran_id'],
        ]);
    }
}
