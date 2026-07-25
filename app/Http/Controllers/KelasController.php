<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KelasController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Kelas/Index', [
            'kelas' => Kelas::query()->orderBy('tingkat')->orderBy('nama')->get(['id', 'nama', 'jurusan', 'tingkat']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Kelas::query()->create($this->validated($request));

        return to_route('kelas.index');
    }

    public function update(Request $request, Kelas $kela): RedirectResponse
    {
        $kela->update($this->validated($request));

        return to_route('kelas.index');
    }

    public function destroy(Kelas $kela): RedirectResponse
    {
        $kela->delete();

        return to_route('kelas.index');
    }

    /**
     * @return array{nama: string, jurusan: string, tingkat: string}
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'jurusan' => ['required', 'string', 'max:255'],
            'tingkat' => ['required', 'string', 'max:255'],
        ]);
    }
}
