@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pengaturan Website</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab">
                                    Umum
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab">
                                    Kontak
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="social-tab" data-toggle="tab" href="#social" role="tab">
                                    Media Sosial
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content mt-3" id="settingsTabContent">
                            @foreach($settings as $group => $groupSettings)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                     id="{{ $group }}"
                                     role="tabpanel">

                                    @foreach($groupSettings as $setting)
                                        <div class="form-group">
                                            <label for="{{ $setting->key }}">
                                                {{ $setting->label }}
                                                @if($setting->description)
                                                    <small class="form-text text-muted">
                                                        {{ $setting->description }}
                                                    </small>
                                                @endif
                                            </label>

                                            @switch($setting->type)
                                                @case('textarea')
                                                    <textarea
                                                        class="form-control @error("settings.{$setting->key}") is-invalid @enderror"
                                                        id="{{ $setting->key }}"
                                                        name="settings[{{ $setting->key }}]"
                                                        rows="3"
                                                    >{{ old("settings.{$setting->key}", $setting->value) }}</textarea>
                                                    @break

                                                @case('file')
                                                    @if($setting->value)
                                                        <div class="mb-2">
                                                            <img src="{{ Storage::url($setting->value) }}"
                                                                 alt="{{ $setting->label }}"
                                                                 class="img-thumbnail"
                                                                 style="max-height: 100px">
                                                        </div>
                                                    @endif
                                                    <input
                                                        type="file"
                                                        class="form-control-file @error("settings.{$setting->key}") is-invalid @enderror"
                                                        id="{{ $setting->key }}"
                                                        name="settings[{{ $setting->key }}]"
                                                    >
                                                    @break

                                                @default
                                                    <input
                                                        type="{{ $setting->type }}"
                                                        class="form-control @error("settings.{$setting->key}") is-invalid @enderror"
                                                        id="{{ $setting->key }}"
                                                        name="settings[{{ $setting->key }}]"
                                                        value="{{ old("settings.{$setting->key}", $setting->value) }}"
                                                    >
                                            @endswitch

                                            @error("settings.{$setting->key}")
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
