<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs(User::factory()->create(['is_active' => true]));
    }

    public function test_settings_round_trip(): void
    {
        $this->putJson('/api/v1/admin/settings', [
            'phone1' => '01097003465',
            'facebook' => 'https://facebook.com/activegroup',
            'stats' => [['value' => 15, 'suffix' => '+', 'label_ar' => 'سنة', 'label_en' => 'Years']],
            'unknown_key' => 'ignored',
        ])->assertOk()->assertJsonPath('data.phone1', '01097003465')->assertJsonMissingPath('data.unknown_key');

        $this->getJson('/api/v1/admin/settings')->assertJsonPath('data.stats.0.value', 15);
        $this->getJson('/api/v1/site')->assertJsonPath('data.social.facebook', 'https://facebook.com/activegroup');
    }

    public function test_settings_are_validated(): void
    {
        $this->putJson('/api/v1/admin/settings', [
            'phone1' => 'call me',
            'facebook' => 'javascript:alert(1)',
            'stats' => [['value' => 'many']],
        ])->assertUnprocessable()->assertJsonValidationErrors(['phone1', 'facebook', 'stats.0.value', 'stats.0.label_ar']);
    }

    public function test_overview_counts_sections_and_missing_english(): void
    {
        $this->postJson('/api/v1/admin/testimonials', ['name_ar' => 'أ', 'quote_ar' => 'رأي']);
        $this->postJson('/api/v1/admin/testimonials', ['name_ar' => 'ب', 'name_en' => 'B', 'quote_ar' => 'رأي', 'quote_en' => 'Quote']);
        $this->postJson('/api/v1/newsletter', ['email' => 'x@example.com']);

        $this->getJson('/api/v1/admin/overview')
            ->assertOk()
            ->assertJsonPath('sections.1.key', 'testimonials')
            ->assertJsonPath('sections.1.total', 2)
            ->assertJsonPath('sections.1.missing_en', 1)
            ->assertJsonPath('subscribers.total', 1);
    }
}
