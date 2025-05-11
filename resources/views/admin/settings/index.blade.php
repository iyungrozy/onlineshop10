@extends('layouts.admin')

@section('title', 'Settings')

@section('header', 'Settings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">General Settings</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Site Name -->
                    <div>
                        <label for="site_name" class="block text-sm font-medium text-gray-700">Site Name</label>
                        <input type="text" name="site_name" id="site_name" value="{{ $settings['site_name'] }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Site Description -->
                    <div>
                        <label for="site_description" class="block text-sm font-medium text-gray-700">Site Description</label>
                        <input type="text" name="site_description" id="site_description" value="{{ $settings['site_description'] }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Contact Email -->
                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-gray-700">Contact Email</label>
                        <input type="email" name="contact_email" id="contact_email" value="{{ $settings['contact_email'] }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Contact Phone -->
                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
                        <input type="text" name="contact_phone" id="contact_phone" value="{{ $settings['contact_phone'] }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- WhatsApp Number -->
                    <div>
                        <label for="whatsapp_number" class="block text-sm font-medium text-gray-700">WhatsApp Number</label>
                        <input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ $settings['whatsapp_number'] }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Social Media Links -->
                    <div>
                        <label for="facebook_url" class="block text-sm font-medium text-gray-700">Facebook URL</label>
                        <input type="url" name="facebook_url" id="facebook_url" value="{{ $settings['facebook_url'] }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="instagram_url" class="block text-sm font-medium text-gray-700">Instagram URL</label>
                        <input type="url" name="instagram_url" id="instagram_url" value="{{ $settings['instagram_url'] }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="twitter_url" class="block text-sm font-medium text-gray-700">Twitter URL</label>
                        <input type="url" name="twitter_url" id="twitter_url" value="{{ $settings['twitter_url'] }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
