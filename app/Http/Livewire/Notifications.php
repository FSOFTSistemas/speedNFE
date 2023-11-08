<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Notifications extends Component
{
    public $notifications = [];

    public function addNotification($message, $type)
    {
        $this->notifications[] = [
            'message' => $message,
            'type' => $type,
        ];

        $this->emit('notificationAdded', end($this->notifications));
    }

    public function removeNotification($index)
    {
        array_splice($this->notifications, $index, 1);

        $this->emit('notificationRemoved', $index);
    }

    public function render()
    {
        return view('livewire.notifications');
    }
}
