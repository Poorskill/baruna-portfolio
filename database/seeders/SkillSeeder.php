<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\SkillGroup;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['name' => 'Frontend Development', 'slug' => 'frontend', 'sort_order' => 1],
            ['name' => 'Backend Development',  'slug' => 'backend',  'sort_order' => 2],
            ['name' => 'Database',             'slug' => 'database', 'sort_order' => 3],
            ['name' => 'Tools & Workflow',     'slug' => 'tools',    'sort_order' => 4],
            ['name' => 'API & Automation',     'slug' => 'api',      'sort_order' => 5],
            ['name' => 'IT Infrastructure',    'slug' => 'infra',    'sort_order' => 6],
        ];

        $skills = [
            'frontend' => [
                ['name' => 'Vue.js 3',       'slug' => 'vuejs-3'],
                ['name' => 'Tailwind CSS',   'slug' => 'tailwind-css'],
                ['name' => 'JavaScript',     'slug' => 'javascript'],
                ['name' => 'TypeScript',     'slug' => 'typescript'],
                ['name' => 'Inertia.js',     'slug' => 'inertiajs'],
                ['name' => 'HTML5 / CSS3',   'slug' => 'html5-css3'],
            ],
            'backend' => [
                ['name' => 'PHP 8.x',        'slug' => 'php'],
                ['name' => 'Laravel',        'slug' => 'laravel'],
                ['name' => 'RESTful APIs',   'slug' => 'restful-apis'],
                ['name' => 'MVC Architecture', 'slug' => 'mvc'],
            ],
            'database' => [
                ['name' => 'MySQL',          'slug' => 'mysql'],
                ['name' => 'PostgreSQL',     'slug' => 'postgresql'],
            ],
            'tools' => [
                ['name' => 'Git & GitHub',   'slug' => 'git-github'],
                ['name' => 'Vite',           'slug' => 'vite'],
                ['name' => 'Composer',       'slug' => 'composer'],
            ],
            'api' => [
                ['name' => 'Webhooks',       'slug' => 'webhooks'],
                ['name' => 'Telegram Bot API', 'slug' => 'telegram-bot-api'],
                ['name' => 'OpenAI API',     'slug' => 'openai-api'],
            ],
            'infra' => [
                ['name' => 'Linux Administration', 'slug' => 'linux'],
                ['name' => 'Docker',         'slug' => 'docker'],
            ],
        ];

        foreach ($groups as $groupData) {
            $group = SkillGroup::firstOrCreate(
                ['slug' => $groupData['slug']],
                $groupData + ['is_visible' => true]
            );

            $groupSkills = $skills[$groupData['slug']] ?? [];
            foreach ($groupSkills as $i => $skillData) {
                Skill::firstOrCreate(
                    ['slug' => $skillData['slug']],
                    $skillData + [
                        'skill_group_id' => $group->id,
                        'sort_order' => $i + 1,
                        'is_visible' => true,
                    ]
                );
            }
        }
    }
}
