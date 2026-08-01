<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GuruController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Guru/Index', [
            'guru' => Guru::query()->orderBy('nama')->get(['id', 'nama', 'mapel']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Guru::query()->create($this->validated($request));

        return to_route('guru.index');
    }

    public function update(Request $request, Guru $guru): RedirectResponse
    {
        $guru->update($this->validated($request));

        return to_route('guru.index');
    }

    public function destroy(Guru $guru): RedirectResponse
    {
        $guru->delete();

        return to_route('guru.index');
    }

    /**
     * @return array{nama: string, mapel: ?string}
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'mapel' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
