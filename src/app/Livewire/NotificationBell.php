<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\InboxNotification;

class NotificationBell extends Component
{
    public $unreadCount = 0;
    public $notifications = [];

    protected $listeners = ['$refresh'];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->unreadCount = InboxNotification::forUser(auth()->id())->unread()->count();

        $this->notifications = InboxNotification::forUser(auth()->id())
            ->with('task')
            ->latest()
            ->take(5)
            ->get()
            ->toArray();
    }

    public function markAsRead($id)
    {
        $notif = InboxNotification::forUser(auth()->id())->findOrFail($id);
        $notif->markAsRead();
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
