<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display all notifications.
     */
    public function index(): View
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->paginate(15);

        return view(
            'notifications.index',
            compact('notifications')
        );
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(string $notification): RedirectResponse
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($notification);

        $notification->markAsRead();

        return back();
    }

    /**
     * Mark all notifications as read.
     */
    public function readAll(): RedirectResponse
    {
        auth()->user()
            ->unreadNotifications
            ->markAsRead();

        return back();
    }

    /**
     * Open a notification.
     */
    public function open(string $notification): RedirectResponse
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($notification);

        if (!$notification->read_at) {
            $notification->markAsRead();
        }

        return redirect(
            $notification->data['url']
                ?? route('notifications.index')
        );
    }
}