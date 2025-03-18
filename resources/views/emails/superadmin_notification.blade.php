<!DOCTYPE html>
<html>
<head>
    <title>Admin User Notification</title>
</head>
<body>
    <h1>Admin User {{ ucfirst($action) }}</h1>
    <p>Admin user {{ $user->name }} ({{ $user->email }}) has been {{ $action }}.</p>
</body>
</html>