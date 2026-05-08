<x-filament::page>
    <div class="space-y-4">
        <x-filament::input.wrapper>
            <x-filament::input.select
                wire:model.live="branchId"
            >
                <option value="">Select branch...</option>
                @foreach (\App\Models\Branch::query()->orderBy('name')->get() as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </x-filament::input.select>
        </x-filament::input.wrapper>
        <br>

        {{ $this->table }}
    </div>
</x-filament::page>