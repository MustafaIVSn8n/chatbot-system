<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\RateLimiter;

class NotificationController extends Controller
{
    /**
     * Get the user's unread notifications
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->unreadNotifications()
            ->latest()
            ->take(20)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'message' => $notification->data['message'] ?? 'New notification',
                    'created_at' => $notification->created_at->diffForHumans(),
                    'read_at' => $notification->read_at,
                ];
            });

        return response()->json($notifications);
    }

    /**
     * Mark a single notification as read
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        RateLimiter::attempt(
            'mark-read:'.$request->user()->id,
            10,
            function() {},
            60
        );

        $notification = $request->user()->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read',
            'notification_id' => $id
        ]);
    }

    /**
     * Mark multiple notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:notifications,id'
        ]);

        $request->user()->notifications()
            ->whereIn('id', $validated['ids'])
            ->update(['read_at' => now()]);

        return response()->json([
            'message' => 'Notifications marked as read',
            'marked_count' => count($validated['ids'])
        ]);
    }
}