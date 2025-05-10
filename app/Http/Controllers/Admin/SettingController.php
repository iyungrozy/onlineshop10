<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        foreach ($request->settings as $key => $value) {
            $setting = Setting::where('key', $key)->first();

            if ($setting && $setting->type === 'file' && $request->hasFile("settings.{$key}")) {
                // Hapus file lama jika ada
                if ($setting->value) {
                    Storage::delete($setting->value);
                }

                // Upload file baru
                $path = $request->file("settings.{$key}")->store('settings');
                $value = $path;
            }

            if ($setting) {
                $setting->update(['value' => $value]);
            }
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan berhasil diperbarui');
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
