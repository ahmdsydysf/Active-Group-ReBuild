<?php

namespace Database\Seeders;

use App\Models\Factory;
use App\Models\Feature;
use App\Models\Partner;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\ProductCategory;
use App\Models\Slide;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Support\Settings;
use Illuminate\Support\Str;

/**
 * Content for going live: the real parts of the old site's database (legacy.json) plus the
 * new version's images, and the new sample content for sections the old site only had
 * placeholder text for (services, advice, gallery). Run once on an empty database:
 *
 *   php artisan db:seed --class=LegacySeeder --force
 *
 * legacy.json was taken from the old database dump. Placeholder text (lorem ipsum),
 * test entries and the spam-only contact messages were left out.
 */
class LegacySeeder extends ContentSeeder
{
    private const SLIDE_IMAGES = ['planting.jpg', 'greenhouse.jpg', 'sprouts.jpg', 'field.jpg'];

    private const FACTORY_IMAGES = ['fertilizer-pack.jpg', 'lab.jpg', 'tools.jpg'];

    private const BLOG_IMAGES = ['lawn.jpg', 'sunflowers.jpg', 'gardener.jpg'];

    // [icon, colour] for the "why us" cards, in order.
    private const FEATURE_LOOK = [['shield', 'field'], ['people', 'bloom'], ['truck', 'sky'], ['lab', 'wheat'], ['people', 'rust']];

    private array $data;

    public function run(): void
    {
        $this->data = json_decode((string) file_get_contents(__DIR__.'/legacy.json'), true, flags: JSON_THROW_ON_ERROR);

        $this->call(SettingsSeeder::class);
        Settings::update($this->data['settings']);

        $this->legacySlides();
        $this->legacyFeatures();
        $this->legacyCatalog();
        $this->legacyTeam();
        $this->legacyPartners();
        $this->legacyTestimonials();
        $this->services();
        $this->gallery();
        $this->samplePosts(['advice']);
        $this->legacyBlog();
    }

    private function legacySlides(): void
    {
        foreach ($this->data['slides'] as $i => $s) {
            Slide::query()->create([
                ...$s,
                'button_ar' => $i === 0 ? 'تصفح المنتجات' : null,
                'button_en' => $i === 0 ? 'Browse products' : null,
                'button_url' => $i === 0 ? '/products' : null,
                'image' => $this->image(self::SLIDE_IMAGES[$i % count(self::SLIDE_IMAGES)], Slide::UPLOAD_DIR),
                'sort_order' => $i,
            ]);
        }
    }

    private function legacyFeatures(): void
    {
        foreach ($this->data['features'] as $i => $f) {
            [$icon, $color] = self::FEATURE_LOOK[$i % count(self::FEATURE_LOOK)];
            Feature::query()->create([...$f, 'icon' => $icon, 'color' => $color, 'sort_order' => $i]);
        }
    }

    private function legacyCatalog(): void
    {
        $factories = array_map(fn ($f, $i) => Factory::query()->create([
            ...$f,
            'image' => $this->image(self::FACTORY_IMAGES[$i], Factory::UPLOAD_DIR),
            'sort_order' => $i,
        ]), $this->data['factories'], array_keys($this->data['factories']));

        $n = 0;
        foreach ($this->data['catalog'] as $c => $cat) {
            $category = ProductCategory::query()->create([
                'slug' => $cat['slug'], 'name_ar' => $cat['name_ar'], 'name_en' => $cat['name_en'],
                'color' => $cat['color'], 'sort_order' => $c,
            ]);

            foreach ($cat['types'] as $t => $type) {
                $row = $category->types()->create(['name_ar' => $type['name_ar'], 'name_en' => $type['name_en'], 'sort_order' => $t]);

                foreach ($type['products'] as $p) {
                    // No photo: the site draws the pack in the category colour until one is uploaded.
                    $row->products()->create([
                        'factory_id' => $factories[$p['factory']]->id,
                        'name_ar' => $p['name_ar'], 'name_en' => $p['name_en'],
                        'summary_ar' => "{$type['name_ar']} من {$cat['name_ar']} أكتيف.",
                        'summary_en' => "{$type['name_en']} from Active {$cat['name_en']}.",
                        'sort_order' => $n++, 'is_featured' => $n <= 3,
                    ]);
                }
            }
        }
    }

    // No photos: the old ones are not available, and stock photos must not stand in for real people.
    private function legacyTeam(): void
    {
        foreach ($this->data['team'] as $i => $m) {
            TeamMember::query()->create([...$m, 'sort_order' => $i]);
        }
    }

    private function legacyPartners(): void
    {
        foreach ($this->data['partners'] as $i => $p) {
            Partner::query()->create([...$p, 'logo' => $this->image($p['logo'], Partner::UPLOAD_DIR), 'sort_order' => $i]);
        }
    }

    private function legacyTestimonials(): void
    {
        $colors = ['field', 'rust', 'sky'];
        foreach ($this->data['testimonials'] as $i => $t) {
            Testimonial::query()->create([...$t, 'color' => $colors[$i % count($colors)], 'sort_order' => $i]);
        }
    }

    private function legacyBlog(): void
    {
        $category = PostCategory::query()->firstOrCreate(
            ['slug' => 'garden-design'],
            ['section' => 'blog', 'name_ar' => 'تنسيق الحدائق', 'name_en' => 'Garden design', 'sort_order' => 0],
        );

        foreach ($this->data['blog'] as $i => $b) {
            Post::query()->create([
                'section' => 'blog', 'post_category_id' => $category->id,
                'title_ar' => $b['title_ar'], 'title_en' => $b['title_en'],
                'excerpt_ar' => Str::limit(trim(html_entity_decode(strip_tags($b['body_ar']))), 160),
                'body_ar' => $b['body_ar'],
                'image' => $this->image(self::BLOG_IMAGES[$i % count(self::BLOG_IMAGES)], Post::UPLOAD_DIR),
                'is_active' => true, 'is_featured' => $i === 0,
                'published_at' => $b['published_at'],
            ]);
        }

        // The sample blog categories have no posts here.
        PostCategory::query()->where('section', 'blog')->whereDoesntHave('posts')->delete();
    }
}
