<?php

namespace Tests\Feature;

use App\Models\GalleryPhoto;
use App\Models\Message;
use App\Models\Post;
use App\Models\ProductType;
use App\Models\Setting;
use App\Models\User;
use App\Support\Publisher;
use Database\Seeders\ContentSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminSectionsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed([SettingsSeeder::class, ContentSeeder::class]);
        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'is_active' => true]);
        Sanctum::actingAs($this->admin);
    }

    public function test_a_product_saves_dose_rows_sent_as_json_text(): void
    {
        $type = ProductType::query()->first();

        $this->post('/api/v1/admin/products', [
            'product_type_id' => $type->id,
            'name_ar' => 'منتج تجربة',
            'doses_ar' => json_encode([['crop' => 'الطماطم', 'rate' => '1 لتر']]),
            'body_ar' => '<p>نص</p><script>x</script>',
            'image' => UploadedFile::fake()->image('p.jpg', 800, 600),
            'leaflet' => UploadedFile::fake()->create('leaflet.pdf', 200, 'application/pdf'),
        ], ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('data.doses_ar.0.crop', 'الطماطم')
            ->assertJsonPath('data.body_ar', '<p>نص</p>')
            ->assertJsonPath('data.slug', fn ($s) => $s !== '');

        $this->getJson('/api/v1/admin/products?status=missing_en')->assertJsonPath('meta.total', 2);
    }

    public function test_publishing_an_article_without_a_date_dates_it_now(): void
    {
        $id = $this->post('/api/v1/admin/posts', [
            'section' => 'blog', 'title_ar' => 'خبر', 'is_active' => 1, 'image' => UploadedFile::fake()->image('a.jpg'),
        ], ['Accept' => 'application/json'])->assertCreated()->assertJsonPath('data.status', 'published')->json('data.id');

        $this->assertNotNull(Post::query()->find($id)->published_at);

        $this->putJson("/api/v1/admin/posts/{$id}", ['is_active' => true, 'published_at' => now()->addWeek()->toDateTimeString()])
            ->assertJsonPath('data.status', 'scheduled');
        $this->getJson('/api/v1/admin/posts?section=blog&status=scheduled')->assertJsonPath('meta.total', 1);
    }

    public function test_bulk_actions_and_content_changes_mark_the_site_for_publishing(): void
    {
        $ids = GalleryPhoto::query()->limit(3)->pluck('id')->all();
        Setting::query()->updateOrCreate(['key' => 'site_published_at'], ['value' => now()->addMinute()->toIso8601String()]);
        $this->assertFalse(Publisher::status()['pending']);

        $this->travel(2)->minutes();
        $this->postJson('/api/v1/admin/gallery-photos/bulk', ['ids' => $ids, 'action' => 'hide'])->assertNoContent();
        $this->assertSame(3, GalleryPhoto::query()->where('is_active', false)->count());
        $this->assertTrue(Publisher::status()['pending']);

        $this->postJson('/api/v1/admin/gallery-photos/bulk', ['ids' => $ids, 'action' => 'delete'])->assertNoContent();
        $this->assertSame(9, GalleryPhoto::query()->count());
    }

    public function test_gallery_upload_adds_many_photos_at_the_end(): void
    {
        $this->post('/api/v1/admin/gallery-photos/upload', [
            'images' => [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.png')],
        ], ['Accept' => 'application/json'])->assertCreated()->assertJsonCount(2, 'data');

        $this->assertSame(14, GalleryPhoto::query()->count());
        $this->assertSame(GalleryPhoto::query()->max('sort_order'), GalleryPhoto::query()->latest('id')->value('sort_order'));
    }

    public function test_opening_a_message_marks_it_read_and_reply_needs_an_email(): void
    {
        $message = Message::query()->first();

        $this->getJson('/api/v1/admin/messages')->assertJsonPath('counts.new', 5);
        $this->getJson("/api/v1/admin/messages/{$message->id}")->assertJsonPath('data.status', 'read');
        $this->getJson('/api/v1/admin/messages')->assertJsonPath('counts.new', 4);
        $this->postJson("/api/v1/admin/messages/{$message->id}/reply", ['body' => 'أهلا'])->assertUnprocessable();

        $this->putJson("/api/v1/admin/messages/{$message->id}", ['status' => 'archived'])->assertOk();
        $this->getJson('/api/v1/admin/messages?box=archived')->assertJsonPath('meta.total', 1);
        $this->get('/api/v1/admin/messages/export?box=archived')->assertOk()->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_subscribers_can_be_unsubscribed_and_exported(): void
    {
        $id = $this->getJson('/api/v1/admin/subscribers')->json('data.0.id');
        $this->putJson("/api/v1/admin/subscribers/{$id}", ['subscribed' => false])->assertOk();
        $this->getJson('/api/v1/admin/subscribers?status=unsubscribed')->assertJsonPath('meta.total', 1)->assertJsonPath('stats.active', 2);
    }

    public function test_inviting_a_user_emails_a_set_password_link_that_works_once(): void
    {
        Notification::fake();

        $this->postJson('/api/v1/admin/users', ['name' => 'منى', 'email' => 'Mona@Example.com', 'role' => 'editor'])
            ->assertCreated()->assertJsonPath('data.email', 'mona@example.com')->assertJsonPath('invite_sent', true);

        $user = User::query()->where('email', 'mona@example.com')->first();
        $token = null;
        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $n) use (&$token, $user) {
            $token = $n->token;

            return str_contains($n->toMail($user)->actionUrl, '/admin/reset-password/?token=');
        });

        $body = ['token' => $token, 'email' => 'mona@example.com', 'password' => 'new-pass-2026', 'password_confirmation' => 'new-pass-2026'];
        $this->postJson('/api/v1/admin/reset-password', $body)->assertOk();
        $this->assertTrue(Hash::check('new-pass-2026', $user->fresh()->password));
        $this->postJson('/api/v1/admin/reset-password', $body)->assertUnprocessable();
    }

    public function test_forgot_password_answers_the_same_for_unknown_emails(): void
    {
        Notification::fake();

        $this->postJson('/api/v1/admin/forgot-password', ['email' => 'nobody@example.com'])->assertOk();
        $this->postJson('/api/v1/admin/forgot-password', ['email' => $this->admin->email])->assertOk();
        Notification::assertSentTimes(ResetPassword::class, 1);
    }

    public function test_admins_cannot_lock_themselves_out(): void
    {
        $this->putJson("/api/v1/admin/users/{$this->admin->id}", ['is_active' => false])->assertUnprocessable();
        $this->putJson("/api/v1/admin/users/{$this->admin->id}", ['role' => 'editor'])->assertUnprocessable();
        $this->deleteJson("/api/v1/admin/users/{$this->admin->id}")->assertUnprocessable();
    }

    public function test_publish_reports_when_it_is_not_configured(): void
    {
        $this->getJson('/api/v1/admin/publish')->assertOk()->assertJsonPath('data.configured', false)->assertJsonPath('data.pending', true);
        $this->postJson('/api/v1/admin/publish')->assertStatus(503);
    }
}
