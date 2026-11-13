<?php

namespace Tests\Feature;

use App\Models\Partner;
use App\Models\Slide;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Sanctum::actingAs(User::factory()->create(['role' => 'editor', 'is_active' => true]));
    }

    public function test_a_slide_needs_an_image_and_gets_webp_copies(): void
    {
        $this->post('/api/v1/admin/slides', ['title_ar' => 'عنوان'], ['Accept' => 'application/json'])
            ->assertUnprocessable()->assertJsonValidationErrors('image');

        $res = $this->post('/api/v1/admin/slides', [
            'title_ar' => 'عنوان',
            'image' => UploadedFile::fake()->image('hero.jpg', 2400, 1200),
        ], ['Accept' => 'application/json'])->assertCreated();

        $path = Slide::query()->first()->image;
        $this->assertStringEndsWith('.webp', $path);
        Storage::disk('public')->assertExists([$path, str_replace('.webp', '-sm.webp', $path)]);
        $this->assertSame(1600, getimagesizefromstring(Storage::disk('public')->get($path))[0]);
        $res->assertJsonPath('data.missing_en', true);
    }

    public function test_svg_and_fake_images_are_rejected(): void
    {
        $svg = UploadedFile::fake()->createWithContent('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');
        $fake = UploadedFile::fake()->createWithContent('logo.png', '<?php echo 1;');

        foreach ([$svg, $fake] as $file) {
            $this->post('/api/v1/admin/partners', ['name_ar' => 'شريك', 'logo' => $file], ['Accept' => 'application/json'])
                ->assertUnprocessable()->assertJsonValidationErrors('logo');
        }
    }

    public function test_replacing_an_image_removes_the_old_files(): void
    {
        $this->post('/api/v1/admin/partners', ['name_ar' => 'شريك', 'logo' => UploadedFile::fake()->image('a.png')], ['Accept' => 'application/json'])->assertCreated();
        $partner = Partner::query()->first();
        $old = $partner->logo;

        $this->post("/api/v1/admin/partners/{$partner->id}", ['_method' => 'PUT', 'logo' => UploadedFile::fake()->image('b.png')], ['Accept' => 'application/json'])->assertOk();

        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($partner->refresh()->logo);
    }

    public function test_a_required_image_cannot_be_removed(): void
    {
        $this->post('/api/v1/admin/slides', ['title_ar' => 'س', 'image' => UploadedFile::fake()->image('a.jpg')], ['Accept' => 'application/json']);
        $slide = Slide::query()->first();

        $this->putJson("/api/v1/admin/slides/{$slide->id}", ['remove_image' => true])->assertOk();

        $this->assertNotNull($slide->refresh()->image);
    }

    public function test_deleting_a_record_deletes_its_images(): void
    {
        $this->post('/api/v1/admin/slides', ['title_ar' => 'س', 'image' => UploadedFile::fake()->image('a.jpg')], ['Accept' => 'application/json']);
        $slide = Slide::query()->first();

        $this->deleteJson("/api/v1/admin/slides/{$slide->id}")->assertNoContent();

        Storage::disk('public')->assertMissing($slide->image);
        $this->assertDatabaseCount('slides', 0);
    }

    public function test_new_records_go_last_and_reorder_saves_positions(): void
    {
        foreach (['أ', 'ب', 'ج'] as $name) {
            $this->postJson('/api/v1/admin/testimonials', ['name_ar' => $name, 'quote_ar' => 'رأي'])->assertCreated();
        }
        [$a, $b, $c] = Testimonial::query()->orderBy('id')->pluck('id')->all();

        $this->postJson('/api/v1/admin/testimonials/reorder', ['ids' => [$c, $a, $b]])->assertNoContent();

        $this->getJson('/api/v1/admin/testimonials')->assertJsonPath('data.*.name_ar', ['ج', 'أ', 'ب']);
    }

    public function test_testimonial_colour_must_be_from_the_palette(): void
    {
        $this->postJson('/api/v1/admin/testimonials', ['name_ar' => 'أ', 'quote_ar' => 'رأي', 'color' => 'red; background:url(x)'])
            ->assertUnprocessable()->assertJsonValidationErrors('color');
    }

    public function test_slide_links_must_be_site_paths_or_web_urls(): void
    {
        $this->postJson('/api/v1/admin/slides', ['title_ar' => 'س', 'button_url' => 'javascript:alert(1)'])
            ->assertUnprocessable()->assertJsonValidationErrors('button_url');
    }

    public function test_search_filters_the_list(): void
    {
        $this->postJson('/api/v1/admin/testimonials', ['name_ar' => 'سعيد', 'quote_ar' => 'رأي']);
        $this->postJson('/api/v1/admin/testimonials', ['name_ar' => 'هالة', 'quote_ar' => 'رأي']);

        $this->getJson('/api/v1/admin/testimonials?search=هال')->assertJsonCount(1, 'data')->assertJsonPath('meta.total', 1);
    }
}
