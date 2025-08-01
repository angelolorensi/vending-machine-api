<x-filament-panels::page>
    <!-- Filament will automatically render header widgets at the top -->
    
    <!-- Main Widgets (Charts and Tables) -->
    <div class="grid gap-6 lg:grid-cols-2">
        @foreach ($this->getWidgets() as $widget)
            @livewire($widget)
        @endforeach
    </div>
</x-filament-panels::page>
