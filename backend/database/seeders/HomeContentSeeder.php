<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\ProductCategory;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\Slide;
use App\Models\Testimonial;
use App\Support\ImageStore;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Starter content for the home page, taken from the approved design.
 * The client replaces it from the dashboard. Safe to run once on an empty database.
 */
class HomeContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->slides();
        $this->catalog();
        $this->services();
        $this->partners();
        $this->testimonials();
    }

    private function image(string $file, string $dir): string
    {
        $source = __DIR__.'/images/'.$file;

        if (str_ends_with($file, '.svg')) {
            $path = "{$dir}/{$file}";
            Storage::disk('public')->put($path, (string) file_get_contents($source));

            return $path;
        }

        return ImageStore::storeFromPath($source, $dir);
    }

    private function slides(): void
    {
        $rows = [
            ['غذاء متكامل للنبات، وحماية من البذرة لحد الحصاد', 'Complete plant nutrition and protection, from seed to harvest',
                'أسمدة ومبيدات ومنظمات نمو مختارة بعناية، يوصلها فريق أكتيف لمزرعتك ومعاها برنامج تسميد ومتابعة من مهندس زراعي.',
                'Carefully chosen fertilizers, pesticides and growth regulators, delivered to your farm with a fertilization program and follow-up from an agronomist.',
                'تصفح المنتجات', 'Browse products', '/products', 'planting.jpg'],
            ['برامج تسميد مكتوبة لكل محصول', 'Written fertilization programs for every crop', null, null,
                'احجز استشارة', 'Book a consultation', '/contact', 'greenhouse.jpg'],
            ['معمل تحليل التربة والمياه', 'Soil and water testing lab', null, null,
                'اعرف أكتر', 'Learn more', '/services', 'sprouts.jpg'],
        ];

        foreach ($rows as $i => [$tAr, $tEn, $sAr, $sEn, $bAr, $bEn, $url, $img]) {
            Slide::query()->create([
                'title_ar' => $tAr, 'title_en' => $tEn, 'subtitle_ar' => $sAr, 'subtitle_en' => $sEn,
                'button_ar' => $bAr, 'button_en' => $bEn, 'button_url' => $url,
                'image' => $this->image($img, Slide::UPLOAD_DIR), 'sort_order' => $i,
            ]);
        }
    }

    private function catalog(): void
    {
        $categories = [
            ['pesticides', 'المبيدات', 'Pesticides', 'حماية المحصول من الفطريات والحشرات والنيماتودا.', 'Crop protection from fungi, insects and nematodes.', 'rust', [
                ['فطرية', 'Fungi', [['أكتي-مكس 72%', 'Acti-Mix 72%'], ['كوبرا 50%', 'Cobra 50%']]],
                ['حشرية', 'Insects', [['لامدا 5%', 'Lambda 5%']]],
                ['نيماتودية', 'Nematodes', [['نيماتو-ستوب', 'Nemato-Stop']]],
                ['حشائش', 'Weeds', []],
            ]],
            ['fertilizers', 'الأسمدة', 'Fertilizers', 'عناصر كبرى وصغرى وأسمدة متخصصة لكل مرحلة نمو.', 'Macro, micro and specialty fertilizers for every growth stage.', 'field', [
                ['عناصر كبرى', 'Macronutrients', [['NPK 20-20-20', 'NPK 20-20-20'], ['بوتاسيوم 50%', 'Potassium 50%'], ['NPK 10-10-40', 'NPK 10-10-40']]],
                ['عناصر صغرى', 'Micronutrients', [['زنك مخلبي 12%', 'Chelated Zinc 12%'], ['بورون 20%', 'Boron 20%']]],
                ['متخصصة', 'Specialty', []],
                ['محسنات تربة', 'Soil conditioners', [['هيوميك 80%', 'Humic 80%']]],
            ]],
            ['growth-regulators', 'منظمات النمو', 'Growth regulators', 'تحسين العقد والتزهير وحجم الثمار وجودتها.', 'Better flowering, fruit set, fruit size and quality.', 'wheat', [
                ['تزهير', 'Flowering', []],
                ['عقد', 'Fruit set', [['جبريلين 10%', 'Gibberellin 10%']]],
                ['تلوين', 'Colouring', [['سايتوكينين بلس', 'Cytokinin Plus']]],
            ]],
        ];

        foreach ($categories as $c => [$slug, $nAr, $nEn, $dAr, $dEn, $color, $types]) {
            $category = ProductCategory::query()->create([
                'slug' => $slug, 'name_ar' => $nAr, 'name_en' => $nEn, 'description_ar' => $dAr, 'description_en' => $dEn,
                'color' => $color, 'sort_order' => $c,
            ]);

            foreach ($types as $t => [$tAr, $tEn, $products]) {
                $type = $category->types()->create(['name_ar' => $tAr, 'name_en' => $tEn, 'sort_order' => $t]);
                foreach ($products as $p => [$pAr, $pEn]) {
                    $type->products()->create(['name_ar' => $pAr, 'name_en' => $pEn, 'sort_order' => $p]);
                }
            }
        }
    }

    private function services(): void
    {
        $types = collect([
            ['consultation', 'استشارة فنية', 'Technical consultation'],
            ['technical-tip', 'نصيحة فنية', 'Technical tip'],
            ['general-tip', 'نصيحة عامة', 'General tip'],
            ['farming-tip', 'نصيحة زراعية', 'Farming tip'],
        ])->mapWithKeys(fn ($t, $i) => [$t[0] => ServiceType::query()->create(['slug' => $t[0], 'name_ar' => $t[1], 'name_en' => $t[2], 'sort_order' => $i])]);

        // Newest first on the site, so the list is written oldest first.
        $rows = [
            ['consultation', 'اختيار السماد المركب حسب تحليل التربة', 'Choosing a compound fertilizer from your soil test', null, null, 'fertilizer-pack.jpg'],
            ['general-tip', 'تخزين المبيدات بأمان في المزرعة', 'Storing pesticides safely on the farm', null, null, 'tools.jpg'],
            ['technical-tip', 'علامات نقص الزنك على أوراق الذرة، وإزاي تعالجها بسرعة', 'Signs of zinc deficiency on maize leaves, and how to fix it fast', null, null, 'sprouts.jpg'],
            ['farming-tip', 'مواعيد الري المناسبة في موجات الحر', 'When to water during heatwaves', null, null, 'watering.jpg'],
            ['consultation', 'برنامج تسميد الطماطم في العروة الصيفية', 'Fertilizing summer tomatoes',
                'إزاي توزّع العناصر الكبرى على مراحل النمو، وإمتى تضيف الكالسيوم عشان تتجنب عفن الطرف الزهري.',
                'How to split macronutrients across growth stages, and when to add calcium to avoid blossom end rot.', 'planting.jpg'],
        ];

        foreach ($rows as $i => [$type, $tAr, $tEn, $eAr, $eEn, $img]) {
            Service::query()->create([
                'service_type_id' => $types[$type]->id,
                'title_ar' => $tAr, 'title_en' => $tEn, 'excerpt_ar' => $eAr, 'excerpt_en' => $eEn,
                'image' => $this->image($img, Service::UPLOAD_DIR),
                'published_at' => now()->subDays(10 - $i),
            ]);
        }
    }

    private function partners(): void
    {
        $rows = [
            ['Egypt Atanor', 'Egypt Atanor', 'مبيدات', 'Pesticides', 'egypt-atanor.svg'],
            ['Tervalis', 'Tervalis', 'أسمدة فوسفاتية', 'Phosphate fertilizers', 'tervalis.svg'],
            ['Genrong', 'Genrong', 'مبيدات ومنظمات نمو', 'Pesticides and growth regulators', 'genrong.svg'],
            ['Nile Green', 'Nile Green', 'أسمدة', 'Fertilizers', 'nile-green.svg'],
            ['الناصر', 'Al Nasser', 'توزيع وتعبئة', 'Distribution and packing', 'alnasser.svg'],
        ];

        foreach ($rows as $i => [$nAr, $nEn, $fAr, $fEn, $logo]) {
            Partner::query()->create([
                'name_ar' => $nAr, 'name_en' => $nEn, 'field_ar' => $fAr, 'field_en' => $fEn,
                'logo' => $this->image($logo, Partner::UPLOAD_DIR), 'sort_order' => $i,
            ]);
        }
    }

    private function testimonials(): void
    {
        $rows = [
            ['م. فاروق العدلي', 'Eng. Farouk El-Adly', 'مدير جمعية زراعية', 'Farming co-op manager',
                'مش أول مرة أتعامل معاكم. شركة محترمة، والمهندس متابع معايا من أول الموسم لآخره.',
                'I’ve worked with them for years. A decent company, and the engineer follows up with me from the start of the season to the end.', 'field'],
            ['أ. محمود سالم', 'Mr. Mahmoud Salem', 'مزارع عنب، النوبارية', 'Grape grower, Nubaria',
                'البرنامج اللي عملوه للعنب فرق في العقد والحجم بشكل واضح السنة دي.',
                'Their grape program made a clear difference to fruit set and size this year.', 'rust'],
            ['م. هالة عبد الرحمن', 'Eng. Hala Abdelrahman', 'شركة تصدير خضر', 'Vegetable export company',
                'التوريد في ميعاده دايمًا، ودي أهم حاجة في وقت الرش.',
                'Deliveries always arrive on time, and that matters most in spraying season.', 'sky'],
            ['الحاج سعيد مرسي', 'Hajj Saeed Morsy', 'مزارع طماطم، البحيرة', 'Tomato grower, Beheira',
                'أول مرة أعرف إن تحليل التربة يوفر عليّا نص تكلفة السماد.',
                'I didn’t know a soil test could cut my fertilizer bill in half.', 'plum'],
            ['م. ياسر حمدي', 'Eng. Yasser Hamdy', 'مزارع خيار، الإسماعيلية', 'Cucumber grower, Ismailia',
                'المهندس شرح لنا توقيت الرش الصح، والإصابة بالبياض قلت بشكل واضح.',
                'The engineer showed us the right spray timing, and mildew dropped a lot.', 'deep'],
        ];

        foreach ($rows as $i => [$nAr, $nEn, $rAr, $rEn, $qAr, $qEn, $color]) {
            Testimonial::query()->create([
                'name_ar' => $nAr, 'name_en' => $nEn, 'role_ar' => $rAr, 'role_en' => $rEn,
                'quote_ar' => $qAr, 'quote_en' => $qEn, 'color' => $color, 'sort_order' => $i,
            ]);
        }
    }
}
