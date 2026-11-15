<?php

namespace Database\Seeders;

use App\Support\Settings;
use Illuminate\Database\Seeder;

/** Contact details and numbers from the current site and the approved design. */
class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Settings::update([
            'site_name_ar' => 'أكتيف جروب',
            'site_name_en' => 'Active Group',
            'footer_about_ar' => 'نوفّر الأسمدة والمبيدات ومنظمات النمو للمزارع المصرية، ومعها متابعة فنية من مهندسينا في الحقل.',
            'footer_about_en' => 'We supply fertilizers, pesticides and growth regulators to Egyptian farms, with technical follow-up from our engineers in the field.',
            'phone1' => '01097003465',
            'phone2' => '01274077377',
            'whatsapp' => '201097003465',
            'email' => 'admin@activegroup-eg.com',
            'inbox_email' => 'admin@activegroup-eg.com',
            'address_ar' => 'المنطقة الحرة، مدينة نصر، القاهرة',
            'address_en' => 'Free Zone, Nasr City, Cairo',
            'stats' => [
                ['value' => 15, 'suffix' => '+', 'label_ar' => 'سنة في السوق المصري', 'label_en' => 'Years in the Egyptian market'],
                ['value' => 3, 'suffix' => '', 'label_ar' => 'مصانع للإنتاج والتعبئة', 'label_en' => 'Production and packing plants'],
                ['value' => 1200, 'suffix' => '+', 'label_ar' => 'مزارع وجمعية بنخدمهم', 'label_en' => 'Farms and co-ops we serve'],
                ['value' => 22, 'suffix' => '', 'label_ar' => 'محافظة نوصل لها', 'label_en' => 'Governorates we deliver to'],
            ],
            'hours_ar' => 'السبت إلى الخميس، 9 الصبح لـ 5 المغرب',
            'hours_en' => 'Saturday to Thursday, 9 am to 5 pm',
            'about_title_ar' => '15 سنة جنب المزارع المصري',
            'about_title_en' => '15 years beside Egyptian farmers',
            'about_body_ar' => "أكتيف جروب لتوريد وتجارة الأسمدة والمبيدات. بدأنا لأن المزارع محتاج حد يفهم أرضه قبل ما يبيع له. عشان كده كل منتج عندنا معاه نشرة فنية، وكل عميل معاه مهندس يتابعه.\n\nالنهاردة عندنا 3 مصانع للتعبئة، وشبكة توزيع في 22 محافظة، وشراكات مع شركات عالمية في المبيدات والأسمدة.",
            'about_body_en' => "Active Group supplies and trades fertilizers and pesticides. We started because a farmer needs someone who understands the land before selling to it. That is why every product comes with a technical leaflet, and every customer has an engineer who follows up.\n\nToday we run 3 packing plants, a distribution network in 22 governorates, and partnerships with international pesticide and fertilizer companies.",
            'timeline' => [
                ['year' => 2009, 'title_ar' => 'البداية', 'title_en' => 'The start', 'text_ar' => 'بدأنا كموزع للمبيدات في القاهرة والجيزة.', 'text_en' => 'We started as a pesticide distributor in Cairo and Giza.'],
                ['year' => 2013, 'title_ar' => 'أول مصنع تعبئة', 'title_en' => 'First packing plant', 'text_ar' => 'افتتاح مصنع العبور لتعبئة الأسمدة السائلة.', 'text_en' => 'We opened the Obour plant for liquid fertilizers.'],
                ['year' => 2018, 'title_ar' => 'التوسع للصعيد', 'title_en' => 'Into Upper Egypt', 'text_ar' => 'فروع توزيع في المنيا وأسيوط وسوهاج.', 'text_en' => 'Distribution branches in Minya, Assiut and Sohag.'],
                ['year' => 2024, 'title_ar' => 'معمل التحليل', 'title_en' => 'The testing lab', 'text_ar' => 'معمل تحليل تربة ومياه لخدمة عملائنا.', 'text_en' => 'A soil and water lab for our customers.'],
            ],
            'seo_title_ar' => 'أسمدة ومبيدات ومنظمات نمو | أكتيف جروب',
            'seo_title_en' => 'Fertilizers, pesticides and growth regulators | Active Group',
            'seo_description_ar' => 'أكتيف جروب لتوريد وتجارة الأسمدة والمبيدات ومنظمات النمو في مصر، مع متابعة فنية من مهندسين زراعيين.',
            'seo_description_en' => 'Active Group supplies fertilizers, pesticides and growth regulators across Egypt, with technical follow-up from agronomists.',
        ]);
    }
}
