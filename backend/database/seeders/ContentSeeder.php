<?php

namespace Database\Seeders;

use App\Models\Factory;
use App\Models\Feature;
use App\Models\GalleryAlbum;
use App\Models\GalleryPhoto;
use App\Models\Message;
use App\Models\NewsletterSubscriber;
use App\Models\Partner;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;
use App\Models\Slide;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Support\ImageStore;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Starter content for every page, taken from the approved design.
 * The client replaces it from the dashboard. Safe to run once on an empty database.
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->slides();
        $this->catalog();
        $this->services();
        $this->team();
        $this->posts();
        $this->features();
        $this->gallery();
        $this->partners();
        $this->testimonials();
        $this->inbox();
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
        $factories = collect([
            ['مصنع العبور', 'Obour plant', 'تعبئة الأسمدة السائلة والمخلبيات', 'Liquid and chelated fertilizers', 'مدينة العبور، القليوبية', 'Obour City, Qalyubia', 'lab.jpg'],
            ['مصنع العاشر من رمضان', '10th of Ramadan plant', 'خلط وتعبئة الأسمدة الصلبة', 'Mixing and packing solid fertilizers', 'العاشر من رمضان، الشرقية', '10th of Ramadan, Sharqia', 'fertilizer-pack.jpg'],
            ['مصنع حلوان', 'Helwan plant', 'تجهيز وتعبئة المبيدات', 'Preparing and packing pesticides', 'حلوان، القاهرة', 'Helwan, Cairo', 'tools.jpg'],
        ])->map(fn ($f, $i) => Factory::query()->create([
            'name_ar' => $f[0], 'name_en' => $f[1], 'description_ar' => $f[2], 'description_en' => $f[3],
            'address_ar' => $f[4], 'address_en' => $f[5], 'image' => $this->image($f[6], Factory::UPLOAD_DIR), 'sort_order' => $i,
        ]));
        [$obour, $tenth, $helwan] = $factories->all();

        // [category slug, ar, en, description ar, description en, colour, [[type ar, type en, [[product ar, product en, pack ar, pack en, ingredient ar, ingredient en, factory, image, crops]]]]]
        $categories = [
            ['pesticides', 'المبيدات', 'Pesticides', 'حماية المحصول من الفطريات والحشرات والنيماتودا.', 'Crop protection from fungi, insects and nematodes.', 'rust', [
                ['مبيد فطري', 'Fungicide', [
                    ['أكتي-مكس 72%', 'Acti-Mix 72%', '250 جم · 500 جم · 1 كجم', '250 g · 500 g · 1 kg', 'مانكوزيب 64% + سيموكسانيل 8%', 'Mancozeb 64% + Cymoxanil 8%', $helwan, 'tools.jpg', ['tomato', 'potato', 'grape', 'cucumber']],
                    ['كوبرا 50%', 'Cobra 50%', '500 جم', '500 g', 'أوكسي كلورور النحاس 50%', 'Copper oxychloride 50%', $helwan, 'boots.jpg', ['citrus', 'grape']],
                ]],
                ['مبيد حشري', 'Insecticide', [['لامدا 5%', null, '250 مل', '250 ml', 'لامدا سيهالوثرين 5%', 'Lambda-cyhalothrin 5%', $helwan, 'gardener.jpg', ['maize', 'tomato']]]],
                ['مبيد نيماتودا', 'Nematicide', [['نيماتو-ستوب', 'Nemato-Stop', '1 لتر', '1 L', 'أبامكتين 1.8%', 'Abamectin 1.8%', $helwan, 'hands-soil.jpg', ['tomato', 'potato']]]],
            ]],
            ['fertilizers', 'الأسمدة', 'Fertilizers', 'عناصر كبرى وأسمدة متخصصة لكل مرحلة نمو.', 'Macronutrients and specialty fertilizers for every growth stage.', 'field', [
                ['عناصر كبرى', 'Macronutrients', [
                    ['NPK 20-20-20', 'NPK 20-20-20', '25 كجم', '25 kg', 'نيتروجين 20% · فوسفور 20% · بوتاسيوم 20%', 'N 20% · P 20% · K 20%', $tenth, 'fertilizer-pack.jpg', ['tomato', 'wheat', 'citrus']],
                    ['بوتاسيوم 50%', 'Potassium 50%', '25 كجم', '25 kg', 'سلفات البوتاسيوم 50%', 'Potassium sulphate 50%', $tenth, 'field.jpg', ['citrus', 'grape']],
                    ['NPK 10-10-40', 'NPK 10-10-40', '25 كجم', '25 kg', 'نيتروجين 10% · فوسفور 10% · بوتاسيوم 40%', 'N 10% · P 10% · K 40%', $tenth, 'sunflowers.jpg', ['tomato', 'potato']],
                ]],
                ['محسن تربة', 'Soil conditioner', [['هيوميك 80%', 'Humic 80%', '1 كجم', '1 kg', 'هيومات البوتاسيوم 80%', 'Potassium humate 80%', $obour, 'hands-soil-2.jpg', ['wheat', 'maize']]]],
            ]],
            ['growth-regulators', 'منظمات النمو', 'Growth regulators', 'تحسين العقد والتزهير وحجم الثمار وجودتها.', 'Better flowering, fruit set, fruit size and quality.', 'wheat', [
                ['تحسين العقد', 'Fruit set', [['جبريلين 10%', 'Gibberellin 10%', '100 مل', '100 ml', 'حمض الجبريليك 10%', 'Gibberellic acid 10%', $obour, 'sprouts.jpg', ['grape', 'citrus']]]],
                ['تكبير الثمار', 'Fruit sizing', [['سايتوكينين بلس', 'Cytokinin Plus', '250 مل', '250 ml', 'سايتوكينين 0.1%', 'Cytokinin 0.1%', $obour, 'planting.jpg', ['grape', 'tomato']]]],
            ]],
            ['micronutrients', 'العناصر الصغرى', 'Micronutrients', 'زنك وبورون وحديد مخلبي لعلاج نقص العناصر بسرعة.', 'Chelated zinc, boron and iron to fix deficiencies fast.', 'sky', [
                ['عناصر صغرى', 'Micronutrients', [
                    ['زنك مخلبي 12%', 'Chelated Zinc 12%', '1 كجم', '1 kg', 'زنك EDTA 12%', 'Zinc EDTA 12%', $obour, 'watering.jpg', ['maize', 'wheat']],
                    ['بورون 20%', 'Boron 20%', '1 لتر', '1 L', 'بورون إيثانول أمين 20%', 'Boron ethanolamine 20%', $obour, 'watering-2.jpg', ['tomato', 'grape']],
                ]],
            ]],
        ];

        $crops = [
            'tomato' => ['الطماطم', 'Tomato'], 'potato' => ['البطاطس', 'Potato'], 'grape' => ['العنب', 'Grape'], 'cucumber' => ['الخيار', 'Cucumber'],
            'citrus' => ['الموالح', 'Citrus'], 'maize' => ['الذرة', 'Maize'], 'wheat' => ['القمح', 'Wheat'],
        ];

        $n = 0;
        foreach ($categories as $c => [$slug, $nAr, $nEn, $dAr, $dEn, $color, $types]) {
            $category = ProductCategory::query()->create([
                'slug' => $slug, 'name_ar' => $nAr, 'name_en' => $nEn, 'description_ar' => $dAr, 'description_en' => $dEn,
                'color' => $color, 'sort_order' => $c,
            ]);

            foreach ($types as $t => [$tAr, $tEn, $products]) {
                $type = $category->types()->create(['name_ar' => $tAr, 'name_en' => $tEn, 'sort_order' => $t]);
                foreach ($products as [$pAr, $pEn, $packAr, $packEn, $ingAr, $ingEn, $factory, $img, $cropKeys]) {
                    $rows = fn (int $l) => array_map(fn ($k) => [
                        'crop' => $crops[$k][$l], 'pest' => null, 'rate' => $l ? '250 g / 100 L water' : '250 جم / 100 لتر ماء',
                        'interval' => $l ? '7 to 10 days' : '7 إلى 10 أيام', 'phi' => $l ? '7 days' : '7 أيام',
                    ], $cropKeys);
                    $type->products()->create([
                        'factory_id' => $factory->id, 'name_ar' => $pAr, 'name_en' => $pEn, 'packs_ar' => $packAr, 'packs_en' => $packEn,
                        'active_ingredient_ar' => $ingAr, 'active_ingredient_en' => $ingEn,
                        'summary_ar' => "{$tAr} من {$nAr} أكتيف، بنشرة فنية ومعدلات استخدام لكل محصول.",
                        'summary_en' => $pEn ? "{$tEn} from Active {$nEn}, with a technical leaflet and rates for each crop." : null,
                        'doses_ar' => $rows(0), 'doses_en' => $pEn ? $rows(1) : null,
                        'image' => $this->image($img, Product::UPLOAD_DIR), 'sort_order' => $n++, 'is_featured' => $n <= 3,
                    ]);
                }
            }
        }

        // The design's sample product page, in full.
        Product::query()->where('name_en', 'Acti-Mix 72%')->first()?->update([
            'formulation_ar' => 'مسحوق قابل للبلل WP', 'formulation_en' => 'Wettable powder (WP)',
            'registration_no' => 'EG-1234/2021',
            'summary_ar' => 'مبيد فطري جهازي وملامس، وقائي وعلاجي للندوة المتأخرة والبياض الزغبي. يُرش مع بداية ظروف الإصابة ويكرر كل 7 إلى 10 أيام.',
            'summary_en' => 'A systemic and contact fungicide that prevents and treats late blight and downy mildew. Spray when infection conditions start and repeat every 7 to 10 days.',
            'doses_ar' => [
                ['crop' => 'الطماطم', 'pest' => 'الندوة المتأخرة', 'rate' => '250 جم / 100 لتر ماء', 'interval' => '7 إلى 10 أيام', 'phi' => '7 أيام'],
                ['crop' => 'البطاطس', 'pest' => 'الندوة المتأخرة', 'rate' => '250 جم / 100 لتر ماء', 'interval' => '7 أيام', 'phi' => '14 يوم'],
                ['crop' => 'العنب', 'pest' => 'البياض الزغبي', 'rate' => '200 جم / 100 لتر ماء', 'interval' => '10 أيام', 'phi' => '21 يوم'],
                ['crop' => 'الخيار', 'pest' => 'البياض الزغبي', 'rate' => '250 جم / 100 لتر ماء', 'interval' => '7 أيام', 'phi' => '3 أيام'],
            ],
            'doses_en' => [
                ['crop' => 'Tomato', 'pest' => 'Late blight', 'rate' => '250 g / 100 L water', 'interval' => '7 to 10 days', 'phi' => '7 days'],
                ['crop' => 'Potato', 'pest' => 'Late blight', 'rate' => '250 g / 100 L water', 'interval' => '7 days', 'phi' => '14 days'],
                ['crop' => 'Grape', 'pest' => 'Downy mildew', 'rate' => '200 g / 100 L water', 'interval' => '10 days', 'phi' => '21 days'],
                ['crop' => 'Cucumber', 'pest' => 'Downy mildew', 'rate' => '250 g / 100 L water', 'interval' => '7 days', 'phi' => '3 days'],
            ],
            'faqs_ar' => [
                ['q' => 'ينفع أخلطه مع مبيدات تانية؟', 'a' => 'يخلط مع أغلب المبيدات الحشرية، ماعدا المركبات القلوية زي الجير والكبريت الميكروني. اعمل اختبار خلط صغير الأول.'],
                ['q' => 'إمتى أبدأ الرش؟', 'a' => 'مع بداية الظروف المناسبة للإصابة: رطوبة عالية وحرارة بين 15 و22 درجة، قبل ظهور الأعراض.'],
                ['q' => 'هل يأثر على النحل؟', 'a' => 'سميته منخفضة على النحل، لكن يفضل الرش في الصبح بدري أو بعد العصر.'],
            ],
            'faqs_en' => [
                ['q' => 'Can I mix it with other pesticides?', 'a' => 'It mixes with most insecticides, except alkaline products such as lime and micronized sulphur. Try a small test mix first.'],
                ['q' => 'When should I start spraying?', 'a' => 'When conditions favour infection: high humidity and 15 to 22 °C, before symptoms show.'],
                ['q' => 'Is it harmful to bees?', 'a' => 'Its toxicity to bees is low, but spray early in the morning or late in the afternoon.'],
            ],
            'safety_ar' => "البس جوانتي وكمامة أثناء التحضير والرش.\nما ترشش وقت الرياح الشديدة.\nاحفظه بعيد عن الأطفال والأكل.\nالتزم بفترة الأمان قبل الحصاد.",
            'safety_en' => "Wear gloves and a mask while mixing and spraying.\nDo not spray in strong wind.\nKeep away from children and food.\nRespect the pre-harvest interval.",
        ]);
    }

    private function services(): void
    {
        $rows = [
            ['lab', 'تحليل التربة والمياه', 'Soil and water testing', 'نحلل عينة من أرضك ومياه الري، ونطلع لك تقرير بالعناصر الناقصة والملوحة ودرجة الحموضة.', 'We test a sample of your soil and irrigation water and report missing nutrients, salinity and pH.', 'lab.jpg'],
            ['leaf', 'برامج التسميد', 'Fertilization programs', 'برنامج تسميد مكتوب لكل مرحلة من مراحل المحصول، بالكميات والمواعيد.', 'A written plan for each crop stage, with amounts and dates.', 'fertilizer-pack.jpg'],
            ['shield', 'برامج المكافحة', 'Pest control programs', 'برنامج رش وقائي وعلاجي حسب الآفات المنتشرة في منطقتك وموسمك.', 'A preventive and curative spray plan for the pests in your area and season.', 'boots.jpg'],
            ['visit', 'زيارات ميدانية', 'Field visits', 'مهندس بيزور المزرعة ويشخص المشكلة على الطبيعة ويتابع التنفيذ.', 'An engineer visits the farm, diagnoses the problem on site and follows up.', 'gardener.jpg'],
            ['training', 'تدريب المزارعين', 'Farmer training', 'ندوات وورش عمل في الجمعيات الزراعية عن الاستخدام الآمن للمبيدات.', 'Talks and workshops at farming co-ops on safe pesticide use.', 'planting.jpg'],
            ['truck', 'التوريد للمشاريع', 'Project supply', 'توريد بكميات كبيرة للمشاريع والشركات بجدول تسليم ثابت.', 'Bulk supply for projects and companies on a fixed delivery schedule.', 'lawn.jpg'],
        ];

        foreach ($rows as $i => [$icon, $tAr, $tEn, $sAr, $sEn, $img]) {
            Service::query()->create([
                'icon' => $icon, 'title_ar' => $tAr, 'title_en' => $tEn, 'summary_ar' => $sAr, 'summary_en' => $sEn,
                'image' => $this->image($img, Service::UPLOAD_DIR), 'sort_order' => $i,
            ]);
        }
    }

    private function team(): void
    {
        $rows = [
            ['م. أحمد سيد', 'Eng. Ahmed Sayed', 'المدير العام', 'General manager', 'team-1.jpg'],
            ['م. كريم محمود', 'Eng. Karim Mahmoud', 'مدير المبيعات', 'Sales manager', 'team-2.jpg'],
            ['م. منى فؤاد', 'Eng. Mona Fouad', 'رئيس القسم الفني', 'Head of technical', 'team-3.jpg'],
            ['د. سارة علي', 'Dr. Sara Ali', 'مديرة المعمل', 'Lab manager', 'team-4.jpg'],
        ];

        foreach ($rows as $i => [$nAr, $nEn, $rAr, $rEn, $img]) {
            TeamMember::query()->create([
                'name_ar' => $nAr, 'name_en' => $nEn, 'role_ar' => $rAr, 'role_en' => $rEn,
                'photo' => $this->image($img, TeamMember::UPLOAD_DIR), 'sort_order' => $i,
            ]);
        }
    }

    private function posts(): void
    {
        $category = fn (string $section, string $slug, string $ar, string $en, int $i) => PostCategory::query()->create(['section' => $section, 'slug' => $slug, 'name_ar' => $ar, 'name_en' => $en, 'sort_order' => $i]);
        $advice = [
            'consult' => $category('advice', 'consultations', 'استشارة فنية', 'Technical consultation', 0),
            'farming' => $category('advice', 'farming-tips', 'نصيحة زراعية', 'Farming tip', 1),
            'technical' => $category('advice', 'technical-tips', 'نصيحة فنية', 'Technical tip', 2),
            'general' => $category('advice', 'general-tips', 'نصيحة عامة', 'General tip', 3),
            'field' => $category('advice', 'field-notes', 'خبرة ميدانية', 'Field note', 4),
        ];
        $blog = [
            'news' => $category('blog', 'company-news', 'أخبار الشركة', 'Company news', 0),
            'modern' => $category('blog', 'modern-farming', 'الزراعة الحديثة', 'Modern farming', 1),
            'events' => $category('blog', 'events', 'المعارض والندوات', 'Events', 2),
            'stories' => $category('blog', 'success-stories', 'قصص نجاح', 'Success stories', 3),
        ];
        $author = TeamMember::query()->where('name_en', 'Eng. Mona Fouad')->value('id');

        $tomato = <<<'HTML'
<p>الطماطم من أكتر المحاصيل اللي بتتأثر بتوقيت التسميد. نفس الكمية لو اتحطت في وقت غلط ممكن تدي نمو خضري زيادة على حساب العقد. في المقال ده هنمشي مع النبات مرحلة مرحلة.</p>
<h2>المرحلة الأولى: من الشتل لحد التزهير</h2>
<p>التركيز هنا على الفوسفور عشان الجذور، مع نيتروجين معتدل.</p>
<ul><li>سوبر فوسفات في تجهيز الأرض.</li><li>هيوميك أسيد مع أول رية بعد الشتل.</li><li>NPK 20-20-20 بمعدل 2 كجم للفدان كل أسبوع.</li></ul>
<blockquote>نصيحة: ما تزودش النيتروجين في الأسبوعين اللي قبل التزهير، لأنه بيأخر العقد.</blockquote>
<h2>المرحلة التانية: التزهير والعقد</h2>
<p>هنا بيبدأ دور الكالسيوم والبورون. نقص الكالسيوم هو السبب الأول لعفن الطرف الزهري.</p>
<table><thead><tr><th>السماد</th><th>المعدل للفدان</th><th>التكرار</th></tr></thead><tbody><tr><td>نترات كالسيوم</td><td>5 كجم</td><td>أسبوع</td></tr><tr><td>بورون 20%</td><td>250 مل رش</td><td>10 أيام</td></tr></tbody></table>
<h2>المرحلة التالتة: كبر الثمار والتلوين</h2>
<p>البوتاسيوم هو البطل هنا. استخدم NPK 10-10-40 أو سلفات بوتاسيوم، وقلل النيتروجين تدريجيًا.</p>
HTML;
        $tomatoEn = <<<'HTML'
<p>Tomatoes react strongly to fertilizer timing. The same amount at the wrong time can push leaf growth at the cost of fruit set. Here we follow the plant stage by stage.</p>
<h2>Stage one: transplanting to flowering</h2>
<p>Focus on phosphorus for the roots, with moderate nitrogen.</p>
<ul><li>Superphosphate when preparing the soil.</li><li>Humic acid with the first watering after transplanting.</li><li>NPK 20-20-20 at 2 kg per feddan every week.</li></ul>
<blockquote>Tip: do not raise nitrogen in the two weeks before flowering, it delays fruit set.</blockquote>
<h2>Stage two: flowering and fruit set</h2>
<p>Calcium and boron matter most now. Calcium shortage is the main cause of blossom end rot.</p>
<h2>Stage three: fruit sizing and colour</h2>
<p>Potassium leads here. Use NPK 10-10-40 or potassium sulphate, and cut nitrogen gradually.</p>
HTML;
        $para = fn (string $text) => '<p>'.$text.'</p>';

        // [section, category, title ar, title en, excerpt ar, excerpt en, image, days ago, body ar, body en]
        $rows = [
            ['advice', $advice['field'], 'تجربتنا مع النيماتودا في أراضي النوبارية', 'Our nematode trial in Nubaria', 'برنامج 3 مراحل خفّض الإصابة في الجذور بنسبة واضحة.', 'A three-step program clearly cut root infection.', 'hands-soil.jpg', 30, null, null],
            ['advice', $advice['consult'], 'اختيار السماد المركب حسب تحليل التربة', 'Choosing a compound fertilizer from your soil test', 'تحليل التربة هو اللي يحدد النسبة، مش الإعلان ولا الجار.', 'Your soil test sets the ratio, not an advert or a neighbour.', 'fertilizer-pack.jpg', 20, null, null],
            ['advice', $advice['general'], 'تخزين المبيدات بأمان في المزرعة', 'Storing pesticides safely on the farm', 'مكان جاف وبعيد عن الشمس والأكل والأطفال، والعبوة الأصلية دايمًا.', 'A dry place away from sun, food and children, always in the original pack.', 'tools.jpg', 15, null, null],
            ['advice', $advice['technical'], 'علامات نقص الزنك على أوراق الذرة', 'Signs of zinc deficiency on maize leaves', 'خطوط صفرا بين العروق في الأوراق الحديثة؟ ده غالبًا نقص زنك.', 'Yellow stripes between the veins of young leaves usually mean zinc shortage.', 'sprouts.jpg', 10, null, null],
            ['advice', $advice['farming'], 'مواعيد الري المناسبة في موجات الحر', 'When to water during heatwaves', 'الري بدري الصبح أو بالليل بيقلل الفاقد بالبخر ويحمي الجذور.', 'Watering early in the morning or at night cuts evaporation and protects roots.', 'watering.jpg', 6, null, null],
            ['advice', $advice['consult'], 'برنامج تسميد الطماطم في العروة الصيفية', 'Fertilizing summer tomatoes', 'إزاي توزّع العناصر الكبرى على مراحل النمو، وإمتى تضيف الكالسيوم.', 'How to split macronutrients across growth stages, and when to add calcium.', 'planting.jpg', 2, $tomato, $tomatoEn],
            ['blog', $blog['stories'], 'مزارع موالح في المنوفية حسّن لون الثمار', 'A Menoufia citrus grower improved fruit colour', 'برنامج البوتاسيوم سهّل التصدير من أول موسم.', 'A potassium program made export easier from the first season.', 'field.jpg', 40, null, null],
            ['blog', $blog['modern'], 'الري بالتنقيط: إمتى يستاهل التكلفة؟', 'Drip irrigation: when is it worth the cost?', 'حسبة بسيطة توضح إمتى يرجع لك تمن الشبكة.', 'A simple sum that shows when the system pays for itself.', 'watering-2.jpg', 25, null, null],
            ['blog', $blog['news'], 'افتتاح معمل تحليل التربة والمياه', 'Our soil and water lab is open', 'نتايج التحليل خلال 5 أيام عمل لكل عملائنا.', 'Results within 5 working days for every customer.', 'lab.jpg', 12, null, null],
            ['blog', $blog['events'], 'منتجات جديدة وشراكات من معرض صحارى الزراعي 2026', 'New products and partners from Sahara Expo 2026', 'شاركنا بجناح فيه أحدث منتجاتنا من الأسمدة المخلبية، وقابلنا مئات المزارعين والموزعين.', 'Our stand showed our newest chelated fertilizers, and we met hundreds of farmers and distributors.', 'sunflowers.jpg', 4, null, null],
        ];

        foreach ($rows as [$section, $cat, $tAr, $tEn, $eAr, $eEn, $img, $days, $bAr, $bEn]) {
            Post::query()->create([
                'section' => $section, 'post_category_id' => $cat->id, 'author_id' => $author,
                'title_ar' => $tAr, 'title_en' => $tEn, 'excerpt_ar' => $eAr, 'excerpt_en' => $eEn,
                'body_ar' => $bAr ?? $para($eAr), 'body_en' => $bEn ?? $para($eEn),
                'image' => $this->image($img, Post::UPLOAD_DIR), 'is_active' => true,
                'is_featured' => $section === 'blog' && $days === 4, 'published_at' => now()->subDays($days),
            ]);
        }
    }

    private function features(): void
    {
        $rows = [
            ['shield', 'field', 'منتجات مسجلة', 'Registered products', 'كل منتج عندنا مسجل في وزارة الزراعة ومعاه شهادة تحليل.', 'Every product is registered with the Ministry of Agriculture and has a test certificate.'],
            ['people', 'bloom', 'مهندس في الحقل', 'An engineer in the field', 'فريق فني بيزور المزرعة ويتابع البرنامج من أول الموسم.', 'Our technical team visits the farm and follows the program from the start of the season.'],
            ['truck', 'sky', 'توريد في ميعاده', 'On-time delivery', 'أسطول توزيع بيوصل لـ 22 محافظة في وقت الرش.', 'Our fleet reaches 22 governorates in spraying season.'],
            ['lab', 'wheat', 'معمل تحليل', 'A testing lab', 'تحليل تربة ومياه يحدد احتياج أرضك بالظبط.', 'Soil and water tests show exactly what your land needs.'],
        ];

        foreach ($rows as $i => [$icon, $color, $tAr, $tEn, $dAr, $dEn]) {
            Feature::query()->create(['icon' => $icon, 'color' => $color, 'title_ar' => $tAr, 'title_en' => $tEn, 'description_ar' => $dAr, 'description_en' => $dEn, 'sort_order' => $i]);
        }
    }

    private function gallery(): void
    {
        $album = fn (string $slug, string $ar, string $en, int $i) => GalleryAlbum::query()->create(['slug' => $slug, 'name_ar' => $ar, 'name_en' => $en, 'sort_order' => $i]);
        $a = [
            'visits' => $album('field-visits', 'زيارات ميدانية', 'Field visits', 0),
            'plants' => $album('factories', 'المصانع', 'Factories', 1),
            'expos' => $album('expos', 'المعارض', 'Expos', 2),
            'training' => $album('training', 'التدريب', 'Training', 3),
        ];
        $rows = [
            ['visits', 'زيارة ميدانية في البحيرة', 'Field visit in Beheira', 'gardener.jpg'],
            ['visits', 'حقل عباد الشمس', 'Sunflower field', 'sunflowers.jpg'],
            ['plants', 'معمل تحليل التربة', 'Soil testing lab', 'lab.jpg'],
            ['visits', 'تجربة محفز الجذور', 'Root stimulant trial', 'hands-soil-2.jpg'],
            ['visits', 'حقول القمح', 'Wheat fields', 'field.jpg'],
            ['visits', 'ري بالتنقيط', 'Drip irrigation', 'watering-2.jpg'],
            ['training', 'تجهيز الأرض للزراعة', 'Preparing the soil', 'hands-soil.jpg'],
            ['plants', 'صوبة خضار', 'Vegetable greenhouse', 'greenhouse.jpg'],
            ['training', 'ورشة تدريب المزارعين', 'Farmer workshop', 'planting.jpg'],
            ['expos', 'معرض صحارى', 'Sahara Expo', 'fertilizer-pack.jpg'],
            ['expos', 'مشروع لاندسكيب', 'Landscape project', 'lawn.jpg'],
            ['training', 'شتل الطماطم', 'Tomato seedlings', 'sprouts.jpg'],
        ];

        foreach ($rows as $i => [$key, $cAr, $cEn, $img]) {
            GalleryPhoto::query()->create(['gallery_album_id' => $a[$key]->id, 'caption_ar' => $cAr, 'caption_en' => $cEn, 'image' => $this->image($img, GalleryPhoto::UPLOAD_DIR), 'sort_order' => $i]);
        }
    }

    /** A few sample messages and subscribers so the inbox and newsletter pages are not empty on first look. */
    private function inbox(): void
    {
        $rows = [
            ['quote', 'محمد عبد الله', '01001234567', 'المنيا', 'محتاج سعر 50 شيكارة NPK للتسليم في المنيا', 12],
            ['consult', 'سارة فؤاد', '01112345678', 'الجيزة', 'عندي اصفرار في أوراق الفلفل بعد الري، أعمل إيه؟', 60],
            ['quote', 'عمرو حسن', '01223456789', 'سوهاج', 'عايز أكون موزع معتمد في محافظة سوهاج', 180],
            ['complaint', 'هبة الشريف', '01534567890', 'القاهرة', 'التوريد اتأخر يومين عن الميعاد', 1500],
            ['jobs', 'كريم محمود', '01045678901', 'القاهرة', 'أرسلت السيرة الذاتية لوظيفة مهندس مبيعات', 1600],
        ];

        foreach ($rows as [$type, $name, $phone, $gov, $body, $minutes]) {
            $m = Message::query()->create(['type' => $type, 'name' => $name, 'phone' => $phone, 'governorate' => $gov, 'body' => $body, 'locale' => 'ar', 'status' => 'new']);
            $m->forceFill(['created_at' => now()->subMinutes($minutes)])->save();
        }

        foreach (['farmer.one@example.com', 'coop.minya@example.com', 'agro.store@example.com'] as $email) {
            NewsletterSubscriber::query()->create(['email' => $email, 'locale' => 'ar']);
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
