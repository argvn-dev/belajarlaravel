<?php

use App\Models\Guru;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated users can manage guru', function () {
    $this->actingAs(User::factory()->create());

    $this->post(route('guru.store'), [
        'nama' => 'Budi Santoso',
        'mapel' => 'Matematika',
    ])->assertRedirect(route('guru.index'));

    $guru = Guru::query()->firstOrFail();

    $this->put(route('guru.update', $guru), [
        'nama' => 'Budi S.',
        'mapel' => null,
    ])->assertRedirect(route('guru.index'));

    $this->assertDatabaseHas('guru', ['id' => $guru->id, 'nama' => 'Budi S.', 'mapel' => null]);

    $this->delete(route('guru.destroy', $guru))->assertRedirect(route('guru.index'));

    $this->assertDatabaseMissing('guru', ['id' => $guru->id]);
});
