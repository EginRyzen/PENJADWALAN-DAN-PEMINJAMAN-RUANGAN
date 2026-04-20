<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    use ApiResponse;

    /**
     * Get list of notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $type = $request->query('type', 'all'); // all, unread
            
            $query = $user->notifications()->orderBy('created_at', 'desc');

            if ($type === 'unread') {
                $query->whereNull('read_at');
            }

            $notifications = $query->paginate($request->query('per_page', 20));

            return $this->successResponse($notifications, 'Notifications fetched successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($id)
    {
        try {
            $user = Auth::user();
            $notification = $user->notifications()->where('id', $id)->first();

            if (!$notification) {
                return $this->errorResponse('Notification not found', 404);
            }

            $notification->update(['read_at' => now()]);

            return $this->successResponse(null, 'Notification marked as read');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        try {
            $user = Auth::user();
            $user->unreadNotifications()->update(['read_at' => now()]);

            return $this->successResponse(null, 'All notifications marked as read');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get unread notifications count.
     */
    public function unreadCount()
    {
        try {
            $user = Auth::user();
            $count = $user->unreadNotifications()->count();

            return $this->successResponse(['count' => $count], 'Unread count fetched');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
