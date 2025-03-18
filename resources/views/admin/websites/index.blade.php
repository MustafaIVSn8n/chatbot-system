@extends('layouts.admin')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">Assigned Websites</h1>

    @if($websites->count() === 0)
        <div class="alert alert-info">
            You have no assigned websites.
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 g-4">
            @foreach($websites as $website)
                <div class="col">
                    <div class="card h-100 shadow border-0">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">{{ $website->name }}</h5>
                            <p class="text-muted mb-2">URL: {{ $website->url }}</p>
                            <p class="mb-2">
                                <strong>Total Chats:</strong> {{ $website->total_chats }}
                            </p>
                            <!-- Add more analytics as needed, e.g. trial scheduled, closed chats, etc. -->

                            <!-- Example: Link to a page to view or handle the website's chats -->
                            <a href="{{ route('admin.chats.index', ['website_id' => $website->id]) }}" class="btn btn-primary btn-sm">
                                View Chats
                            </a>
                            <a href="{{ route('admin.websites.widget.edit', $website->id) }}" class="btn btn-secondary btn-sm">
                                Manage Widget
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection