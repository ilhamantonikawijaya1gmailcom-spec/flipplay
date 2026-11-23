<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlipPlayApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::create([
            'name' => 'Mahasiswa Demo',
            'email' => 'mahasiswa@example.test',
            'password' => 'password-demo',
        ]);
    }

    private function mediaPayload(string $title = 'Cahaya Kota'): array
    {
        return [
            'title' => $title,
            'type' => 'film',
            'genre' => 'Drama',
            'release_year' => 2026,
            'description' => 'Contoh media',
        ];
    }

    public function test_crud_media_dan_pencarian(): void
    {
        $created = $this->postJson('/api/media', $this->mediaPayload());
        $created->assertCreated()->assertJsonPath('data.title', 'Cahaya Kota');
        $mediaId = $created->json('data.id');

        $this->getJson('/api/media?search=Cahaya')
            ->assertOk()->assertJsonPath('data.0.id', $mediaId);
        $this->getJson('/api/media/'.$mediaId)
            ->assertOk()->assertJsonPath('data.genre', 'Drama');

        $this->patchJson('/api/media/'.$mediaId, ['genre' => 'Komedi'])
            ->assertOk()->assertJsonPath('data.genre', 'Komedi');
        $this->deleteJson('/api/media/'.$mediaId)
            ->assertOk()->assertJsonPath('data', null);
        $this->getJson('/api/media/'.$mediaId)->assertNotFound();
    }

    public function test_validasi_judul_kosong_dan_duplikat(): void
    {
        $this->postJson('/api/media', $this->mediaPayload(''))
            ->assertUnprocessable()->assertJsonValidationErrors('title');
        $this->postJson('/api/media', $this->mediaPayload('Unik'))->assertCreated();
        $this->postJson('/api/media', $this->mediaPayload('Unik'))
            ->assertUnprocessable()->assertJsonValidationErrors('title');
    }

    public function test_koleksi_media_tambah_tampil_duplikat_hapus(): void
    {
        $user = $this->makeUser();
        $media = Media::create($this->mediaPayload());
        $url = '/api/users/'.$user->id.'/collection';

        $this->postJson($url, ['media_id' => $media->id])
            ->assertCreated()->assertJsonPath('data.media.0.id', $media->id);
        $this->getJson('/api/users/'.$user->id)
            ->assertOk()->assertJsonPath('data.media.0.title', 'Cahaya Kota');
        $this->postJson($url, ['media_id' => $media->id])
            ->assertStatus(409);
        $this->deleteJson($url.'/'.$media->id)
            ->assertOk()->assertJsonCount(0, 'data.media');
        $this->deleteJson($url.'/'.$media->id)
            ->assertNotFound();
    }

    public function test_koleksi_media_id_tidak_ada_ditolak(): void
    {
        $user = $this->makeUser();
        $this->postJson('/api/users/'.$user->id.'/collection', ['media_id' => 9999])
            ->assertUnprocessable()->assertJsonValidationErrors('media_id');
    }
}
