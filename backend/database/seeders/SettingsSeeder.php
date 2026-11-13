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
            'seo_title_ar' => 'أسمدة ومبيدات ومنظمات نمو | أكتيف جروب',
            'seo_title_en' => 'Fertilizers, pesticides and growth regulators | Active Group',
            'seo_description_ar' => 'أكتيف جروب لتوريد وتجارة الأسمدة والمبيدات ومنظمات النمو في مصر، مع متابعة فنية من مهندسين زراعيين.',
            'seo_description_en' => 'Active Group supplies fertilizers, pesticides and growth regulators across Egypt, with technical follow-up from agronomists.',
        ]);
    }
}
