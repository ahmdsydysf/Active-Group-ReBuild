<?php

namespace Tests\Feature;

use Database\Seeders\LegacySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LegacySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_old_site_content_seeds_and_reaches_the_site_api(): void
    {
        Storage::fake('public');
        $this->seed(LegacySeeder::class);

        $this->getJson('/api/v1/home')->assertOk()->assertJsonCount(5, 'data.partners');
        $this->getJson('/api/v1/about?lang=en')->assertOk()->assertJsonPath('data.team.0.name', 'Osama Tarek');
        $this->assertDatabaseCount('products', 28);
        $this->assertDatabaseHas('team_members', ['name_ar' => 'محمد السيد', 'photo' => null]);
        $this->assertDatabaseHas('posts', ['section' => 'blog', 'title_en' => 'Lighting and accessories in garden design']);
        $this->assertDatabaseCount('messages', 0);
    }
}
