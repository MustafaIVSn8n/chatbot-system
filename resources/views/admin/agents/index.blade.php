@extends('layouts.admin')

@section('content')
<div class="container my-5">
    <h1 class="mb-4 text-center">Manage Agents</h1>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.agents.create') }}" class="btn btn-primary">Add New Agent</a>
    </div>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($agents as $agent)
            <tr>
                <td>{{ $agent->name }}</td>
                <td>{{ $agent->email }}</td>
                <td>
                    <a href="{{ route('admin.agents.edit', $agent->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.agents.destroy', $agent->id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this agent?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection