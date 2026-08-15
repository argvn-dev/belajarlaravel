<?php

namespace App\Http\Controllers;

use App\Models\Kehadiran;
use App\Models\KehadiranSiswa;
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

    public function edit(Kehadiran $kehadiran): Response
    {
        $kehadiran->load([
            'jadwalPelajaran.kelas:id,nama,jurusan,tingkat',
            'jadwalPelajaran.guru:id,nama,mapel',
            'kehadiranSiswa.siswa:id,nis,nama',
        ]);

        return Inertia::render('Kehadiran/Edit', [
            'kehadiran' => $kehadiran,
        ]);
    }

    public function update(Request $request, Kehadiran $kehadiran): RedirectResponse
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string'],
            'statuses' => ['required', 'array'],
            'statuses.*.id' => ['required', 'integer', 'exists:kehadiran_siswa,id'],
            'statuses.*.status' => ['required', Rule::in(['Hadir', 'Sakit', 'Izin', 'Alpa'])],
        ]);

        $kehadiran->update([
            'tanggal' => $data['tanggal'],
            'keterangan' => $data['keterangan'],
        ]);

        foreach ($data['statuses'] as $item) {
            KehadiranSiswa::query()
                ->where('kehadiran_id', $kehadiran->id)
                ->where('id', $item['id'])
                ->update(['status' => $item['status']]);
        }

        return to_route('kehadiran.index');
    }

    public function destroy(Kehadiran $kehadiran): RedirectResponse
    {
        $kehadiran->kehadiranSiswa()->delete();
        $kehadiran->delete();

        return to_route('kehadiran.index');
    }
}
