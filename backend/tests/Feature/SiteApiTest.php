<?php

namespace Tests\Feature;

use App\Models\Partner;
use App\Models\Testimonial;
use App\Support\Settings;
use Database\Seeders\ContentSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiteApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed([SettingsSeeder::class, ContentSeeder::class]);
    }

    public function test_home_returns_every_section_in_arabic_by_default(): void
    {
        $this->getJson('/api/v1/home')
            ->assertOk()
            ->assertHeader('Cache-Control', 'max-age=60, public')
            ->assertJsonCount(3, 'data.slides')
            ->assertJsonCount(4, 'data.categories')
            ->assertJsonCount(5, 'data.advice')
            ->assertJsonCount(5, 'data.partners')
            ->assertJsonCount(5, 'data.testimonials')
            ->assertJsonPath('data.slides.0.title', 'غذاء متكامل للنبات، وحماية من البذرة لحد الحصاد')
            ->assertJsonPath('data.categories.1.products_count', 4)
            ->assertJsonPath('data.categories.0.types', ['مبيد فطري', 'مبيد حشري', 'مبيد نيماتودا'])
            ->assertJsonPath('data.advice.0.title', 'برنامج تسميد الطماطم في العروة الصيفية');
    }

    public function test_home_in_english_falls_back_to_arabic_when_a_field_is_empty(): void
    {
        Testimonial::query()->first()->update(['name_en' => null]);

        $this->getJson('/api/v1/home?lang=en')
            ->assertJsonPath('data.slides.0.button', 'Browse products')
            ->assertJsonPath('data.testimonials.0.name', 'م. فاروق العدلي');
    }

    public function test_hidden_items_are_left_out(): void
    {
        Partner::query()->first()->update(['is_active' => false]);

        $this->getJson('/api/v1/home')->assertJsonCount(4, 'data.partners');
    }

    public function test_site_data_never_exposes_the_inbox_email(): void
    {
        Settings::update(['inbox_email' => 'private@example.com']);

        $response = $this->getJson('/api/v1/site?lang=en')
            ->assertOk()
            ->assertJsonPath('data.site_name', 'Active Group')
            ->assertJsonPath('data.stats.2.value', 1200)
            ->assertJsonPath('data.stats.2.label', 'Farms and co-ops we serve')
            ->assertJsonPath('data.categories.0', ['slug' => 'pesticides', 'name' => 'Pesticides']);

        $this->assertStringNotContainsString('private@example.com', $response->getContent());
    }

    public function test_api_sends_security_headers(): void
    {
        $this->getJson('/api/v1/site')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY');
    }
}
