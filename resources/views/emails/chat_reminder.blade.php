<!DOCTYPE html>
<html>
<head>
    <title>Chat Reminder</title>
</head>
<body>
    <p>Dear {{ $chat->assignedAgent->name }},</p>

    <p>This is a reminder that chat #{{ $chat->id }} requires your attention.</p>

    <p>Customer Name: {{ $chat->customer_name }}</p>
    <p>Customer Email: {{ $chat->customer_email }}</p>

    <p>Please respond to the chat as soon as possible.</p>

    <p>Thank you!</p>
</body>
</html>