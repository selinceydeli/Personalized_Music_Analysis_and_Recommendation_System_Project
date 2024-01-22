<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Song;
use App\Models\Playlist;
use App\Models\User;

class UnitTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testUserRegistration()
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'your_password',
            'password_confirmation' => 'your_password',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/home');
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    public function testAddSong()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/songs', [
            'title' => 'New Song',
            'duration' => '3:15',
            'album_id' => 1, // Assuming you have an album with ID 1
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('songs', ['title' => 'New Song']);
    }
    public function testAddAlbum()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/albums', [
            'name' => 'New Album',
            'release_year' => 2021,
        ]);
    
        $response->assertStatus(200);
        $this->assertDatabaseHas('albums', ['name' => 'New Album']);
    }
    
    public function testAddPerformer()
    {
        $response = $this->post('/performers', [
            'name' => 'New Performer',
            'genre' => 'Pop',
        ]);
    
        $response->assertStatus(200);
        $this->assertDatabaseHas('performers', ['name' => 'New Performer']);
    }
  
    public function testRating()
    {
        $user = User::factory()->create();
        $song = Song::factory()->create();
        $response = $this->actingAs($user)->post('/rate_song', [
            'song_id' => $song->id,
            'rating' => 4,
        ]);
    
        $response->assertStatus(200);
        $this->assertDatabaseHas('ratings', ['song_id' => $song->id, 'rating' => 4]);
    }

    public function testAddSongToPlaylist()
    {
        $user = User::factory()->create();
        $song = Song::factory()->create();
        $playlist = Playlist::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post('/playlist/add_song', [
            'playlist_id' => $playlist->id,
            'song_id' => $song->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('playlist_song', ['playlist_id' => $playlist->id, 'song_id' => $song->id]);
    }

    public function testAddUserToPlaylist()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $playlist = Playlist::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post('/playlist/add_user', [
            'playlist_id' => $playlist->id,
            'user_id' => $anotherUser->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('playlist_user', ['playlist_id' => $playlist->id, 'user_id' => $anotherUser->id]);
    }
}
