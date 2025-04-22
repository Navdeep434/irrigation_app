@extends('admin.layouts.app-layout')
@section('page-title', 'Settings | Smart Irrigation')
@section('content')

<div class="container mt-4">
    <h2 class="custom-header py-1 px-3 mb-4">Settings</h2>
    <hr>

    <ul class="nav nav-tabs mb-4" id="settingsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">Security</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="preferences-tab" data-bs-toggle="tab" data-bs-target="#preferences" type="button" role="tab">Preferences</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab">Notifications</button>
        </li>
    </ul>

    <div class="tab-content" id="settingsTabContent">
        {{-- Security Tab --}}
        <div class="tab-pane fade show active" id="security" role="tabpanel">
            <h5>Security Settings</h5>
            <form action="" method="POST">
                @csrf

                {{-- Two-Factor Auth --}}
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="twoFactorToggle" name="two_factor" {{ auth()->user()->two_factor_enabled ? 'checked' : '' }}>
                    <label class="form-check-label" for="twoFactorToggle">Enable Two-Factor Authentication</label>
                </div>

                {{-- Change Password --}}
                <div class="mb-3">
                    <label for="current-password" class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="current-password" name="current_password">
                </div>
                <div class="mb-3">
                    <label for="new-password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new-password" name="new_password">
                </div>
                <div class="mb-3">
                    <label for="confirm-password" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm-password" name="new_password_confirmation">
                </div>

                <button type="submit" class="btn btn-primary">Update Security</button>
            </form>
        </div>

        {{-- Preferences Tab --}}
        <div class="tab-pane fade" id="preferences" role="tabpanel">
            <h5>Preferences</h5>
            <form action="" method="POST">
                @csrf

                {{-- Language --}}
                <div class="mb-3">
                    <label for="language" class="form-label">Preferred Language</label>
                    <select class="form-select" id="language" name="language">
                        <option value="en" {{ auth()->user()->language === 'en' ? 'selected' : '' }}>English</option>
                        <option value="hi" {{ auth()->user()->language === 'hi' ? 'selected' : '' }}>Hindi</option>
                        <option value="es" {{ auth()->user()->language === 'es' ? 'selected' : '' }}>Spanish</option>
                        <!-- Add more as needed -->
                    </select>
                </div>

                {{-- Theme --}}
                <div class="mb-3">
                    <label for="theme" class="form-label">Theme</label>
                    <select class="form-select" id="theme" name="theme">
                        <option value="light" {{ auth()->user()->theme === 'light' ? 'selected' : '' }}>Light</option>
                        <option value="dark" {{ auth()->user()->theme === 'dark' ? 'selected' : '' }}>Dark</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Save Preferences</button>
            </form>
        </div>

        {{-- Notifications Tab --}}
        <div class="tab-pane fade" id="notifications" role="tabpanel">
            <h5>Notification Settings</h5>
            <form action="" method="POST">
                @csrf

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="email_notifications" id="emailNotifications" {{ auth()->user()->email_notifications ? 'checked' : '' }}>
                    <label class="form-check-label" for="emailNotifications">Receive Email Notifications</label>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="sms_notifications" id="smsNotifications" {{ auth()->user()->sms_notifications ? 'checked' : '' }}>
                    <label class="form-check-label" for="smsNotifications">Receive SMS Alerts</label>
                </div>

                <button type="submit" class="btn btn-primary">Update Notifications</button>
            </form>
        </div>
    </div>
</div>

@endsection
