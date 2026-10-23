<?php

namespace Database\Seeders;

use App\Models\NavigationLink;
use App\Models\SocialLink;
use Illuminate\Database\Seeder;

class NavigationSeeder extends Seeder
{
    public function run(): void
    {
        $navLinks = [
            ['placement' => 'header', 'label' => 'Home',     'url' => '/',         'is_external' => false, 'sort_order' => 1],
            ['placement' => 'header', 'label' => 'About',    'url' => '/about',    'is_external' => false, 'sort_order' => 2],
            ['placement' => 'header', 'label' => 'Projects', 'url' => '/projects', 'is_external' => false, 'sort_order' => 3],
            ['placement' => 'header', 'label' => 'Contact',  'url' => '/contact',  'is_external' => false, 'sort_order' => 4],
            ['placement' => 'footer', 'label' => 'Projects', 'url' => '/projects', 'is_external' => false, 'sort_order' => 1],
            ['placement' => 'footer', 'label' => 'About',    'url' => '/about',    'is_external' => false, 'sort_order' => 2],
            ['placement' => 'footer', 'label' => 'Contact',  'url' => '/contact',  'is_external' => false, 'sort_order' => 3],
        ];

        foreach ($navLinks as $link) {
            NavigationLink::firstOrCreate(
                ['placement' => $link['placement'], 'label' => $link['label']],
                $link + ['is_visible' => true]
            );
        }

        $social = [
            ['label' => 'GitHub',    'platform' => 'github',    'url' => 'https://github.com',    'sort_order' => 1],
            ['label' => 'LinkedIn',  'platform' => 'linkedin',  'url' => 'https://linkedin.com',  'sort_order' => 2],
            ['label' => 'Instagram', 'platform' => 'instagram', 'url' => 'https://instagram.com', 'sort_order' => 3],
            ['label' => 'WhatsApp',  'platform' => 'whatsapp',  'url' => 'https://wa.me/',        'sort_order' => 4],
        ];

        foreach ($social as $link) {
            SocialLink::firstOrCreate(
                ['platform' => $link['platform']],
                $link + ['is_visible' => true]
            );
        }
    }
}
