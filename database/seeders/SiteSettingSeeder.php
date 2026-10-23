<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['key' => 'site_name',           'value' => 'Baruna Dwi Cahya',              'group' => 'identity'],
            ['key' => 'site_descriptor',     'value' => 'Web Development & Software Systems', 'group' => 'identity'],
            ['key' => 'site_tagline',        'value' => '',                               'group' => 'identity'],
            ['key' => 'contact_email',       'value' => '',                               'group' => 'contact'],
            ['key' => 'contact_whatsapp',    'value' => '',                               'group' => 'contact'],
            ['key' => 'cv_path',             'value' => '',                               'group' => 'identity'],
            ['key' => 'availability_status', 'value' => '',                               'group' => 'identity'],
            ['key' => 'hero_headline',       'value' => 'Pengembangan web dan solusi sistem digital.',  'group' => 'home'],
            ['key' => 'hero_description',    'value' => 'Saya Baruna Dwi Cahya, dengan latar belakang Teknik Informatika dan fokus pada pengembangan aplikasi web serta sistem digital. Portofolio ini menampilkan proyek, implementasi teknis, dan teknologi yang saya gunakan.', 'group' => 'home'],
            ['key' => 'hero_cta_primary_label', 'value' => 'Kenali Saya',               'group' => 'home'],
            ['key' => 'hero_cta_primary_url',   'value' => '/about',                     'group' => 'home'],
            ['key' => 'hero_cta_secondary_label', 'value' => 'Download CV',               'group' => 'home'],
            ['key' => 'hero_cta_secondary_url',  'value' => '/cv',                        'group' => 'home'],
            ['key' => 'about_short',         'value' => '',                               'group' => 'about'],
            ['key' => 'about_long',          'value' => '',                               'group' => 'about'],
            ['key' => 'footer_text',         'value' => '',                               'group' => 'footer'],
            ['key' => 'default_meta_title',       'value' => 'Baruna Dwi Cahya — Web Development & Software Systems', 'group' => 'seo'],
            ['key' => 'default_meta_description', 'value' => 'Portofolio Baruna Dwi Cahya — mahasiswa Teknik Informatika yang membangun aplikasi web dengan Laravel, Vue.js, dan MySQL.', 'group' => 'seo'],
        ];

        foreach ($defaults as $setting) {
            SiteSetting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
