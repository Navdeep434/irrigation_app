@extends('admin.layouts.app-layout')
@section('page-title', 'Settings | Smart Irrigation')
@section('content')
<div class="container mt-4">
    <h2 class="custom-header py-1 px-3 mb-4">Settings</h2>
    <hr>

    {{-- Settings Tabs --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="list-group">
                <a href="#general-settings" class="list-group-item list-group-item-action active" data-bs-toggle="collapse" role="button" aria-expanded="true" aria-controls="general-settings">General Settings</a>
                <a href="#profile-settings" class="list-group-item list-group-item-action" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="profile-settings">Profile Settings</a>
                <a href="#account-settings" class="list-group-item list-group-item-action" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="account-settings">Account Settings</a>
            </div>
        </div>
        
        <div class="col-md-9">
            {{-- General Settings Section --}}
            <div id="general-settings" class="collapse show">
                <h4>General Settings</h4>
                <form action="" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="site-name" class="form-label">Site Name</label>
                        <input type="text" class="form-control" id="site-name" name="site_name" value="" required>
                    </div>

                    <div class="mb-3">
                        <label for="site-description" class="form-label">Site Description</label>
                        <textarea class="form-control" id="site-description" name="site_description" rows="3" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>

            {{-- Profile Settings Section --}}
            <div id="profile-settings" class="collapse">
                <h4>Profile Settings</h4>
                <form action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="profile-picture" class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" id="profile-picture" name="profile_picture">
                    </div>

                    <div class="mb-3">
                        <label for="first-name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first-name" name="first_name" value="" required>
                    </div>

                    <div class="mb-3">
                        <label for="last-name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last-name" name="last_name" value="" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>

            {{-- Account Settings Section --}}
            <div id="account-settings" class="collapse">
                <h4>Account Settings</h4>
                <form action="" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <small class="form-text text-muted">Leave blank to keep the current password.</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
