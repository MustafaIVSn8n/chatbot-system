@extends('layouts.super_admin')

@section('content')
    <div class="container">
        <h1>Admin Users</h1>

        <a href="{{ route('super_admin.users.create') }}" class="btn btn-primary mb-3">Create Admin</a>

        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($admins as $admin)
                    <tr>
                        <td>{{ $admin->name }}</td>
                        <td>{{ $admin->email }}</td>
                        <td>{{ $admin->created_at }}</td>
                        <td>
                            <a href="{{ route('super_admin.users.edit', $admin->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection