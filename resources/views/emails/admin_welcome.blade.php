<!DOCTYPE html>
<html>
<head>
    <title>Welcome to the Admin Panel</title>
</head>
<body>
    <h1>Welcome, {{ $user->name }}!</h1>
    <p>You have been granted access to the admin panel.</p>
    <p>Your email is: {{ $user->email }}</p>
    <p>Your password is: {{ $password }}</p>
    <p>You can log in through this link: <a href="{{ $adminPanelLink }}">{{ $adminPanelLink }}</a></p>

    @if ($websites->count() > 0)
        <p>You have been assigned to the following websites:</p>
        <ul>
            @foreach ($websites as $website)
                <li>{{ $website->name }} ({{ $website->url }})</li>
            @endforeach
        </ul>
    @else
        <p>You have not been assigned to any websites yet.</p>
    @endif

    <p>Please change your password after logging in.</p>
</body>
</html>