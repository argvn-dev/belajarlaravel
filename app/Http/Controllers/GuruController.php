<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\WaliKelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GuruController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Guru/Index', [
            'guru' => Guru::query()->with(['waliKelas.kelas', 'waliKelas.tahunAjaran'])->orderBy('nama')->get(),
            'kelas' => Kelas::query()->orderBy('tingkat')->orderBy('nama')->get(['id', 'nama']),
            'tahunAjaran' => TahunAjaran::query()->orderByDesc('aktif')->latest()->get(['id', 'nama']),
            'waliKelas' => WaliKelas::query()->with(['guru:id,nama', 'kelas:id,nama', 'tahunAjaran:id,nama'])->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $guru = Guru::query()->create($data);
        $this->syncWaliKelas($guru, $data);

        return to_route('guru.index');
    }

    public function update(Request $request, Guru $guru): RedirectResponse
    {
        $data = $this->validated($request);
        $guru->update($data);
        $this->syncWaliKelas($guru, $data);

        return to_route('guru.index');
    }

    public function destroy(Guru $guru): RedirectResponse
    {
        $guru->delete();

        return to_route('guru.index');
    }

    /**
     * @return array{nama: string, mapel: ?string, kelas_id: ?int, tahun_ajaran_id: ?int}
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'mapel' => ['nullable', 'string', 'max:255'],
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
            'tahun_ajaran_id' => ['nullable', 'integer', 'exists:tahun_ajaran,id', 'required_with:kelas_id'],
        ]);
    }

    /**
     * @param  array{kelas_id: ?int, tahun_ajaran_id: ?int}  $data
     */
    private function syncWaliKelas(Guru $guru, array $data): void
    {
        WaliKelas::query()->where('guru_id', $guru->id)->delete();

        if ($data['kelas_id'] && $data['tahun_ajaran_id']) {
            WaliKelas::query()->updateOrCreate(
                ['kelas_id' => $data['kelas_id'], 'tahun_ajaran_id' => $data['tahun_ajaran_id']],
                ['guru_id' => $guru->id],
            );
        }
    }
}
