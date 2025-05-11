<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'site_name' => config('app.name'),
            'site_description' => config('app.description', ''),
            'contact_email' => config('app.contact_email', ''),
            'contact_phone' => config('app.contact_phone', ''),
            'whatsapp_number' => config('app.whatsapp_number', ''),
            'facebook_url' => config('app.facebook_url', ''),
            'instagram_url' => config('app.instagram_url', ''),
            'twitter_url' => config('app.twitter_url', ''),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'whatsapp_number' => 'required|string|max:20',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
        ]);

        // Update settings in config
        foreach ($validated as $key => $value) {
            config(['app.' . $key => $value]);
        }

        // Clear cache
        Cache::forget('settings');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    public function seed()
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'Online Shop Game Digital',
                'group' => 'general',
                'type' => 'text',
                'label' => 'Nama Website',
                'description' => 'Nama website yang akan ditampilkan di browser dan email'
            ],
            [
                'key' => 'site_logo',
                'value' => null,
                'group' => 'general',
                'type' => 'file',
                'label' => 'Logo Website',
                'description' => 'Logo website yang akan ditampilkan di header'
            ],
            [
                'key' => 'site_description',
                'value' => 'Toko online game digital terpercaya',
                'group' => 'general',
                'type' => 'textarea',
                'label' => 'Deskripsi Website',
                'description' => 'Deskripsi website untuk SEO'
            ],
            [
                'key' => 'contact_email',
                'value' => 'admin@example.com',
                'group' => 'contact',
                'type' => 'email',
                'label' => 'Email Kontak',
                'description' => 'Email untuk kontak customer service'
            ],
            [
                'key' => 'contact_phone',
                'value' => '081234567890',
                'group' => 'contact',
                'type' => 'text',
                'label' => 'Nomor Telepon',
                'description' => 'Nomor telepon untuk kontak customer service'
            ],
            [
                'key' => 'contact_address',
                'value' => 'Jl. Contoh No. 123, Jakarta',
                'group' => 'contact',
                'type' => 'textarea',
                'label' => 'Alamat',
                'description' => 'Alamat kantor'
            ],
            [
                'key' => 'social_facebook',
                'value' => 'https://facebook.com/example',
                'group' => 'social',
                'type' => 'text',
                'label' => 'Facebook',
                'description' => 'URL halaman Facebook'
            ],
            [
                'key' => 'social_instagram',
                'value' => 'https://instagram.com/example',
                'group' => 'social',
                'type' => 'text',
                'label' => 'Instagram',
                'description' => 'URL halaman Instagram'
            ],
            [
                'key' => 'social_twitter',
                'value' => 'https://twitter.com/example',
                'group' => 'social',
                'type' => 'text',
                'label' => 'Twitter',
                'description' => 'URL halaman Twitter'
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan default berhasil dibuat');
    }
}
