<?php

use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated users can manage tahun ajaran', function () {
    $this->actingAs(User::factory()->create());

    $this->post(route('tahun-ajaran.store'), [
        'nama' => '2025/2026',
        'aktif' => true,
    ])->assertRedirect(route('tahun-ajaran.index'));

    $tahunAjaran = TahunAjaran::query()->firstOrFail();

    expect($tahunAjaran->aktif)->toBeTrue();

    $this->put(route('tahun-ajaran.update', $tahunAjaran), [
        'nama' => '2026/2027',
        'aktif' => false,
    ])->assertRedirect(route('tahun-ajaran.index'));

    $this->assertDatabaseHas('tahun_ajaran', [
        'id' => $tahunAjaran->id,
        'nama' => '2026/2027',
        'aktif' => false,
    ]);

    $this->delete(route('tahun-ajaran.destroy', $tahunAjaran))
        ->assertRedirect(route('tahun-ajaran.index'));

    $this->assertDatabaseMissing('tahun_ajaran', ['id' => $tahunAjaran->id]);
});
