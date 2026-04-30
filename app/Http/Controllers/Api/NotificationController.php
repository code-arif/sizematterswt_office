<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;

    /**
     * Get all notifications for authenticated user
     * Supports filtering by type
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $type = $request->input('type');

        $query = auth()->user()->notifications();

        if ($type) {
            $notificationClass = $this->getNotificationClass($type);
            if ($notificationClass) {
                $query->where('type', $notificationClass);
            }
        }

        $notifications = $query->latest()->paginate($perPage);

        $formattedNotifications = $notifications->map(function ($notification) {
            return $this->formatNotification($notification);
        });

        $response = [
            'notifications' => $formattedNotifications,
            'pagination' => [
                'total' => $notifications->total(),
                'per_page' => $notifications->perPage(),
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
            ],
            'unread_count' => auth()->user()->unreadNotifications()->count()
        ];

        return $this->success('Notifications retrieved successfully', $response, 200);
    }

    /**
     * Get only unread notifications
     */
    public function unread(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $type = $request->input('type');

        $query = auth()->user()->unreadNotifications();

        if ($type) {
            $notificationClass = $this->getNotificationClass($type);
            if ($notificationClass) {
                $query->where('type', $notificationClass);
            }
        }

        $notifications = $query->latest()->paginate($perPage);

        $formattedNotifications = $notifications->map(function ($notification) {
            return $this->formatNotification($notification);
        });

        $response = [
            'notifications' => $formattedNotifications,
            'pagination' => [
                'total' => $notifications->total(),
                'per_page' => $notifications->perPage(),
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
            ],
        ];

        return $this->success('Unread notifications retrieved successfully', $response, 200);
    }

    /**
     * Get notification counts by type
     */
    public function counts()
    {
        $user = auth()->user();

        $counts = [
            'total_unread' => $user->unreadNotifications()->count(),
            'farm_updates' => $user->unreadNotifications()
                ->where('type', 'App\Notifications\FarmNotification')
                ->count(),
            'ranche_updates' => $user->unreadNotifications()
                ->where('type', 'App\Notifications\RancheNotification')
                ->count(),
        ];

        return $this->success('Notification counts retrieved', $counts, 200);
    }

    /**
     * Get single notification detail
     */
    public function show($notificationId)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $notificationId)
            ->first();

        if (!$notification) {
            return $this->error([], 'Notification not found', 404);
        }

        // Mark as read when viewed
        if (!$notification->read_at) {
            $notification->markAsRead();
        }

        $formattedNotification = $this->formatNotification($notification);

        return $this->success('Notification retrieved successfully', $formattedNotification, 200);
    }

    /**
     * Mark specific notification as read
     */
    public function markAsRead($notificationId)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $notificationId)
            ->first();

        if (!$notification) {
            return $this->error([], 'Notification not found', 404);
        }

        $notification->markAsRead();

        return $this->success('Marked as read', [], 200);
    }

    /**
     * Mark all notifications as read
     * Supports filtering by type
     */
    public function markAllAsRead(Request $request)
    {
        $type = $request->input('type');

        $query = auth()->user()->unreadNotifications();

        if ($type) {
            $notificationClass = $this->getNotificationClass($type);
            if ($notificationClass) {
                $query->where('type', $notificationClass);
            }
        }

        $updated = $query->update(['read_at' => now()]);

        return $this->success('Notifications marked as read', [
            'marked_count' => $updated
        ], 200);
    }

    /**
     * Delete a notification
     */
    public function destroy($notificationId)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $notificationId)
            ->first();

        if (!$notification) {
            return $this->error([], 'Notification not found', 404);
        }

        $notification->delete();

        return $this->success('Notification deleted successfully', [], 200);
    }

    /**
     * Clear all read notifications
     */
    public function clearRead()
    {
        $deleted = auth()->user()
            ->readNotifications()
            ->delete();

        return $this->success('Read notifications cleared', [
            'deleted_count' => $deleted
        ], 200);
    }

    /**
     * Format notification based on its type
     */
    private function formatNotification($notification)
    {
        $data = $notification->data;
        $type = class_basename($notification->type);

        // Base structure
        $formatted = [
            'id' => $notification->id,
            'type' => $this->getNotificationType($notification->type),
            'is_read' => $notification->read_at !== null,
            'read_at' => $notification->read_at,
            'created_at' => $notification->created_at,
        ];

        // Add type-specific data
        switch ($notification->type) {
            case 'App\Notifications\FarmNotification':
                $formatted['data'] = [
                    'farm_id' => $data['farm_id'] ?? null,
                    'farm_name' => $data['farm_name'] ?? null,
                    'action' => $data['action'] ?? null,
                    'message' => $data['message'] ?? null,
                    'thumbnail' => $data['thumbnail'] ?? null,
                    'city' => $data['city'] ?? null,
                ];
                break;

            case 'App\Notifications\RancheNotification':
                $formatted['data'] = [
                    'ranche_id' => $data['ranche_id'] ?? null,
                    'ranche_name' => $data['ranche_name'] ?? null,
                    'action' => $data['action'] ?? null,
                    'message' => $data['message'] ?? null,
                    'thumbnail' => $data['thumbnail'] ?? null,
                    'city' => $data['city'] ?? null,
                ];
                break;

            default:
                $formatted['data'] = $data;
                break;
        }

        return $formatted;
    }

    /**
     * Get notification class from type string
     */
    private function getNotificationClass($type)
    {
        $types = [
            'farm_update' => 'App\Notifications\FarmNotification',
            'ranche_update' => 'App\Notifications\RancheNotification',
        ];

        return $types[$type] ?? null;
    }

    /**
     * Get user-friendly type name from notification class
     */
    private function getNotificationType($class)
    {
        $types = [
            'App\Notifications\FarmNotification' => 'farm_update',
            'App\Notifications\RancheNotification' => 'ranche_update',
        ];

        return $types[$class] ?? 'general';
    }
}
