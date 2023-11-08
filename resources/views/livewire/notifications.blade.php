<div>
    @foreach($notifications as $index => $notification)
        <div class="notification {{ $notification['type'] }}"
             x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 5000)"
             x-on:click="show = false"
             wire:click="removeNotification({{ $index }})"
             wire:model="notifications.{{ $index }}">

             <!-- Usa o método $on do Alpine.js para ouvir os eventos do Livewire -->
             x-on:notificationAdded.window="if ($event.detail.type == '{{ $notification['type'] }}') { show = true; }"
             x-on:notificationRemoved.window="if ($event.detail == {{ $index }}) { show = false; }"

            {{ $notification['message'] }}
        </div>
    @endforeach
</div>
