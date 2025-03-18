@extends('layouts.base')

@section('content')
    <nav class="navbar navbar-expand-md navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Agent Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Website Oversight</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Chat History</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="content-wrapper">
        @yield('content')
    </div>
    
    @stack('scripts')
@endsection