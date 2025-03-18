@extends('layouts.base') 

@section('content')
<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse p-3">
            <h4 class="fw-bold mb-4">Super Admin</h4>
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a class="nav-link" href="{{ route('super_admin.dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <!-- Manage Admins Submenu -->
                <li class="nav-item mb-2">
                    <a class="nav-link d-flex justify-content-between align-items-center" href="#adminSubmenu" data-bs-toggle="collapse" aria-expanded="false" aria-controls="adminSubmenu">
                        <span>
                            <i class="bi bi-people me-2"></i> Manage Admins
                        </span>
                        <i class="bi bi-chevron-down"></i>
                    </a>
                    <div class="collapse" id="adminSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item mb-1">
                                <a class="nav-link" href="{{ route('super_admin.users.create') }}">
                                    Create Admin
                                </a>
                            </li>
                            <li class="nav-item mb-1">
                                <a class="nav-link" href="{{ route('super_admin.users.list') }}">
                                    Modify/Delete Admins
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <!-- Manage Websites Submenu -->
                <li class="nav-item mb-2">
                    <a class="nav-link d-flex justify-content-between align-items-center" href="#websiteSubmenu" data-bs-toggle="collapse" aria-expanded="false" aria-controls="websiteSubmenu">
                        <span>
                            <i class="bi bi-globe me-2"></i> Manage Websites
                        </span>
                        <i class="bi bi-chevron-down"></i>
                    </a>
                    <div class="collapse" id="websiteSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item mb-1">
                                <a class="nav-link" href="{{ route('super_admin.websites.create') }}">
                                    Add New Website
                                </a>
                            </li>
                            <li class="nav-item mb-1">
                                <a class="nav-link" href="{{ route('super_admin.websites.index') }}">
                                    Modify/Delete Websites
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link" href="#">
                        <i class="bi bi-bar-chart-line me-2"></i> Analytics
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link" href="#">
                        <i class="bi bi-gear me-2"></i> Settings
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
            </div>

            <!-- Dashboard Content (Analytics, Stats, etc.) -->
            <div class="card shadow border-0 mb-4">
                <div class="card-body">
                    <h5 class="card-title fw-bold">Analytics Overview</h5>
                    <p class="text-muted">
                        Here you can display charts, stats, or summaries relevant to your chatbot system.
                    </p>
                    <!-- Example placeholder content -->
                    <div class="alert alert-info">
                        Analytics data or charts go here.
                    </div>
                </div>
            </div>

            <div class="card shadow border-0">
                <div class="card-body">
                    <h5 class="card-title fw-bold">Quick Actions</h5>
                    <p class="text-muted">
                        Create or manage Admin accounts, view websites, and more.
                    </p>
                    <div class="d-grid gap-2 d-md-block">
                        <a href="{{ route('super_admin.users.create') }}" class="btn btn-primary">
                            Create Admin
                        </a>
                        <a href="{{ route('super_admin.users.list') }}" class="btn btn-secondary">
                            Modify/Delete Admins
                        </a>
                        <a href="{{ route('super_admin.websites.create') }}" class="btn btn-primary">
                            Add New Website
                        </a>
                        <a href="{{ route('super_admin.websites.index') }}" class="btn btn-secondary">
                            Modify/Delete Websites
                        </a>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>
@endsection
