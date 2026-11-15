<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\Post;
use App\Models\Product;
use Database\Seeders\ContentSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PagesApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed([SettingsSeeder::class, ContentSeeder::class]);
    }

    public function test_catalog_has_every_active_product_with_details(): void
    {
        Product::query()->where('name_en', 'Cobra 50%')->update(['is_active' => false]);

        $this->getJson('/api/v1/catalog?lang=en')
            ->assertOk()
            ->assertJsonCount(4, 'data.categories')
            ->assertJsonCount(3, 'data.factories')
            ->assertJsonCount(11, 'data.products')
            ->assertJsonPath('data.products.0.name', 'Acti-Mix 72%')
            ->assertJsonPath('data.products.0.doses.0.pest', 'Late blight')
            ->assertJsonPath('data.products.0.safety.0', 'Wear gloves and a mask while mixing and spraying.')
            ->assertJsonPath('data.products.0.factory', 'Helwan plant');
    }

    public function test_posts_leave_out_drafts_and_scheduled_articles(): void
    {
        Post::query()->where('section', 'advice')->first()->update(['is_active' => false]);
        Post::query()->where('section', 'advice')->latest('id')->first()->update(['published_at' => now()->addDay()]);

        $this->getJson('/api/v1/posts?section=advice')->assertOk()->assertJsonCount(4, 'data.posts');
        $this->getJson('/api/v1/posts?section=blog')->assertJsonCount(4, 'data.posts')->assertJsonCount(4, 'data.categories');
        $this->getJson('/api/v1/posts?section=other')->assertUnprocessable();
    }

    public function test_article_html_is_sanitized(): void
    {
        $post = Post::query()->first();
        $post->update(['body_ar' => '<p onclick="x()">نص</p><script>alert(1)</script><a href="javascript:alert(1)">رابط</a>']);

        $this->assertStringNotContainsString('script', $post->fresh()->body_ar);
        $this->assertStringNotContainsString('onclick', $post->fresh()->body_ar);
        $this->assertStringNotContainsString('javascript:', $post->fresh()->body_ar);
    }

    public function test_about_and_pages(): void
    {
        $this->getJson('/api/v1/about')
            ->assertOk()
            ->assertJsonCount(2, 'data.paragraphs')
            ->assertJsonCount(4, 'data.timeline')
            ->assertJsonPath('data.timeline.0.year', 2009)
            ->assertJsonCount(4, 'data.features')
            ->assertJsonCount(4, 'data.team');

        $this->getJson('/api/v1/pages')
            ->assertOk()
            ->assertJsonCount(6, 'data.services')
            ->assertJsonCount(4, 'data.albums')
            ->assertJsonCount(12, 'data.photos');
    }

    public function test_contact_form_saves_the_message_and_alerts_the_inbox(): void
    {
        Mail::fake();

        $this->postJson('/api/v1/contact', [
            'type' => 'quote', 'name' => 'محمد', 'phone' => '01001234567', 'governorate' => 'المنيا', 'body' => 'عايز سعر 50 شيكارة',
        ])->assertCreated();

        $this->assertDatabaseHas('messages', ['name' => 'محمد', 'status' => 'new', 'type' => 'quote']);
        $this->assertSame('127.0.0.1', Message::query()->latest('id')->first()->ip);
    }

    public function test_contact_form_drops_bots_and_validates(): void
    {
        $before = Message::query()->count();

        $this->postJson('/api/v1/contact', [
            'type' => 'quote', 'name' => 'bot', 'phone' => '01001234567', 'body' => 'buy now', 'website' => 'http://spam.example',
        ])->assertCreated();
        $this->assertSame($before, Message::query()->count());

        $this->postJson('/api/v1/contact', ['type' => 'hack', 'phone' => 'abc'])
            ->assertUnprocessable()->assertJsonValidationErrors(['type', 'name', 'phone', 'body']);
    }
}
