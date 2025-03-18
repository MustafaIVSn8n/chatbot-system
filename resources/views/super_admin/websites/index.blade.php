@extends('layouts.super_admin')

@section('content')
<div class="container my-5">
    <h1 class="mb-4 text-center">Manage Websites</h1>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('super_admin.websites.create') }}" class="btn btn-primary">Add New Website</a>
    </div>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>URL</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($websites as $website)
            <tr>
                <td>{{ $website->name }}</td>
                <td><a href="{{ $website->url }}" target="_blank">{{ $website->url }}</a></td>
                <td>
                    <a href="{{ route('super_admin.websites.edit', $website->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('super_admin.websites.destroy', $website->id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this website?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection