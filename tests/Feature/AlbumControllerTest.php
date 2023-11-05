<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Album;

class AlbumControllerTest extends TestCase
{
    use RefreshDatabase; // This will ensure that your database is in a known state for each test.

    /** @test */
    public function it_can_show_all_albums()
    {
        // Arrange: Create some albums
        Album::factory()->count(3)->create();

        // Act: Send a request to the index method
        $response = $this->getJson('/api/albums');

        // Assert: Check if the albums are returned
        $response->assertOk()
                 ->assertJsonCount(3);
    }

    /** @test */
    public function it_can_store_an_album()
    {
        // Arrange: Prepare album data
        $albumData = [
            'name' => 'Test Album',
            'is_single' => false,
            'image_url' => 'http://example.com/album.jpg',
        ];

        // Act: Send a post request to the store method
        $response = $this->postJson('/api/albums', $albumData);

        // Assert: Check if the album was created
        $response->assertCreated()
                 ->assertJson(['message' => 'Album added']);
        $this->assertDatabaseHas('albums', $albumData);
    }

    /** @test */
    public function it_can_show_a_single_album()
    {
        // Arrange: Create an album
        $album = Album::factory()->create();

        // Act: Send a get request to the search_id method
        $response = $this->getJson("/api/albums/{$album->id}");

        // Assert: Check if the album is returned
        $response->assertOk()
                 ->assertJson($album->toArray());
    }

    /** @test */
    public function it_can_update_an_album()
    {
        // Arrange: Create an album
        $album = Album::factory()->create();
        $updatedData = ['name' => 'Updated Name'];

        // Act: Send a put request to the update method
        $response = $this->putJson("/api/albums/{$album->id}", $updatedData);

        // Assert: Check if the album was updated
        $response->assertOk()
                 ->assertJson(['message' => 'Album Updated']);
        $this->assertDatabaseHas('albums', $updatedData);
    }

    /** @test */
    public function it_can_delete_an_album()
    {
        // Arrange: Create an album
        $album = Album::factory()->create();

        // Act: Send a delete request to the destroy method
        $response = $this->deleteJson("/api/albums/{$album->id}");

        // Assert: Check if the album was deleted
        $response->assertOk()
                 ->assertJson(['message' => 'Album deleted']);
        $this->assertDatabaseMissing('albums', ['id' => $album->id]);
    }
}
