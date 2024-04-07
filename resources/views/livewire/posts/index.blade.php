<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use App\Models\Post;
use App\Models\User;

new class extends Component {
    use Toast, WithPagination;

    public string $search = '';
    public int $author_id = 0;
    public bool $drawer = false;
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    // Clear filters
    public function clear(): void
    {
        $this->warning('Filters cleared', position: 'toast-bottom');
        $this->reset();
        $this->resetPage();
    }

    // Delete action
    public function delete(Post $post): void
    {
        $post->delete();
        $this->warning("$post->name deleted", 'Good bye!', position: 'toast-bottom');
    }

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'name', 'label' => 'Name', 'class' => 'w-64'],
            ['key' => 'author_name', 'label' => 'Author'],
        ];
    }

    public function posts(): LengthAwarePaginator
    {
        return Post::query()
        ->withAggregate('author', 'name')
        ->orderBy(...array_values($this->sortBy))
        ->when($this->search, fn(Builder $q) => $q->where('name', 'like', "%$this->search%"))
        ->when($this->author_id, fn(Builder $q) => $q->where('author_id', $this->author_id))
        ->paginate(8);
    }

    public function with(): array
    {
        return [
            'posts' => $this->posts(),
            'headers' => $this->headers(),
            'users' => User::all(),
        ];
    }

    // Reset pagination when any component property changes
    public function updated($property): void
    {
        if (! is_array($property) && $property != "") {
            $this->resetPage();
        }
    }
}; ?>

<div>
    <!-- HEADER -->
    {{-- class="sticky top-6 z-10 bg-gray-50" --}}
    <x-header title="Posts" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel" />
            <x-button label="Create" link="/posts/create" responsive icon="o-plus" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->
    <x-card>
        <x-table :headers="$headers" :rows="$posts" :sort-by="$sortBy" with-pagination link="posts/{id}/edit">
            @scope('actions', $post)
            <x-button icon="o-trash" wire:click="delete({{ $post['id'] }})" wire:confirm="Are you sure?" spinner class="btn-ghost btn-sm text-red-500" />
            @endscope
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Search..." wire:model.live.debounce="search" icon="o-magnifying-glass" @keydown.enter="$wire.drawer = false" />
            <x-select placeholder="Author" wire:model.live="author_id" :options="$users" icon="o-flag" placeholder-value="0" />
        </div>

        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
</div>
