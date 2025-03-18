<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Website;

class WebsiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test website with ID 1 for widget testing
        Website::create([
            'id' => 1,
            'name' => 'Test Website',
            'url' => 'http://localhost:8000',
            'api_key' => 'test_api_key_' . md5(uniqid()),
            'widget_color' => '#3490dc',
            'widget_position' => 'right',
            'is_active' => true,
            'website_type' => 'test',
            'description' => 'This is a test website for the chat widget'
        ]);

        Website::create([
            'name' => 'Iqra Virtual School',
            'url' => 'https://iqravirtualschool.com',
            'api_key' => 'ivs_api_key_' . md5(uniqid()),
            'widget_color' => '#38c172',
            'widget_position' => 'right',
            'is_active' => true,
            'website_type' => 'education',
            'description' => 'Virtual school offering online education'
        ]);

        Website::create([
            'name' => 'Quran Home Tutor',
            'url' => 'https://quranhometutor.com',
            'api_key' => 'qht_api_key_' . md5(uniqid()),
            'widget_color' => '#6574cd',
            'widget_position' => 'right',
            'is_active' => true,
            'website_type' => 'education',
            'description' => 'Home tutoring for Quran studies'
        ]);

        Website::create([
            'name' => 'Tuition Services',
            'url' => 'https://tuitionservices.com',
            'api_key' => 'ts_api_key_' . md5(uniqid()),
            'widget_color' => '#f6993f',
            'widget_position' => 'right',
            'is_active' => true,
            'website_type' => 'education',
            'description' => 'General tuition services for various subjects'
        ]);
    }
}
