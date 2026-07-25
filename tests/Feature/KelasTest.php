<?php

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated users can manage kelas', function () {
    $this->actingAs(User::factory()->create());

    $this->post(route('kelas.store'), [
        'nama' => 'X IPA 1',
        'jurusan' => 'IPA',
        'tingkat' => 'X',
    ])->assertRedirect(route('kelas.index'));

    $kelas = Kelas::query()->firstOrFail();

    $response = $this->put(route('kelas.update', $kelas), [
        'nama' => 'XI IPA 1',
        'jurusan' => 'IPA',
        'tingkat' => 'XI',
    ]);

    $response->assertRedirect(route('kelas.index'));

    $this->assertDatabaseHas('kelas', ['id' => $kelas->id, 'nama' => 'XI IPA 1']);

    $this->delete(route('kelas.destroy', $kelas))->assertRedirect(route('kelas.index'));

    $this->assertDatabaseMissing('kelas', ['id' => $kelas->id]);
});
