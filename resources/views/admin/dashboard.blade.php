@extends('layouts.base')

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- Optionally, include a sidebar for the Admin Dashboard if you have one --}}
        <nav id="adminSidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse p-3">
            <h4 class="fw-bold mb-4">Admin Panel</h4>
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                {{-- Manage Admins (Agents) Submenu --}}
                {{-- 
                <li class="nav-item mb-2">
                    <a class="nav-link d-flex justify-content-between align-items-center" href="#agentsSubmenu" data-bs-toggle="collapse" aria-expanded="false" aria-controls="agentsSubmenu">
                        <span>
                            <i class="bi bi-person-lines-fill me-2"></i> Manage Agents
                        </span>
                        <i class="bi bi-chevron-down"></i>
                    </a>
                    <div class="collapse" id="agentsSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item mb-1">
                                <a class="nav-link" href="{{ route('admin.agents.create') }}">
                                    Create Agent
                                </a>
                            </li>
                            <li class="nav-item mb-1">
                                <a class="nav-link" href="{{ route('admin.agents.index') }}">
                                    Modify/Delete Agents
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                --}}
                {{-- Chats --}}
            </ul>
        </nav>

        {{-- Main Content for Admin Dashboard --}}
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-grid gap-2 d-md-block">
                <a href="{{ route('admin.websites.index') }}" class="btn btn-primary">
                    Assigned Websites
                </a>
            </div>
            {{-- Dashboard Header --}}
            <div class="bg-primary text-white p-4 rounded-3 mb-4 shadow-sm">
                <h1 class="display-5 fw-bold mb-1">Admin Dashboard</h1>
                <p class="mb-0">Monitor chats and manage agents for your assigned websites.</p>
            </div>

            {{-- Chat Conversations List --}}
            <div class="card shadow border-0">
                <div class="card-header">
                    <h5 class="fw-bold mb-0">Chat Conversations</h5>
                </div>
                <div class="card-body">
                    {{-- Placeholder for chats list --}}
                    {{-- You can loop through a variable passed from your controller (e.g., $chats) --}}
                    @if(isset($chats) && $chats->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Website</th>
                                        <th>Customer</th>
                                        <th>Status</th>
                                        <th>Started At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($chats as $chat)
                                        <tr>
                                            <td>{{ $chat->id }}</td>
                                            <td>{{ $chat->website->name ?? 'N/A' }}</td>
                                            <td>{{ $chat->customer_name }}</td>
                                            <td>{{ ucfirst($chat->status) }}</td>
                                            <td>{{ $chat->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <a href="{{ route('admin.chats.show', $chat->id) }}" class="btn btn-sm btn-info">
                                                    View
                                                </a>
                                                {{-- You can add additional actions here --}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No chats found.
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>
@endsection