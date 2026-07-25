<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TahunAjaranController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('TahunAjaran/Index', [
            'tahunAjaran' => TahunAjaran::query()->latest()->get(['id', 'nama', 'aktif']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'aktif' => ['required', 'boolean'],
        ]);

        if ($data['aktif']) {
            TahunAjaran::query()->update(['aktif' => false]);
        }

        TahunAjaran::query()->create($data);

        return to_route('tahun-ajaran.index');
    }

    public function update(Request $request, TahunAjaran $tahunAjaran): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'aktif' => ['required', 'boolean'],
        ]);

        if ($data['aktif']) {
            TahunAjaran::query()->whereKeyNot($tahunAjaran)->update(['aktif' => false]);
        }

        $tahunAjaran->update($data);

        return to_route('tahun-ajaran.index');
    }

    public function destroy(TahunAjaran $tahunAjaran): RedirectResponse
    {
        $tahunAjaran->delete();

        return to_route('tahun-ajaran.index');
    }
}
