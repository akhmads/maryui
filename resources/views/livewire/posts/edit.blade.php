<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Mary\Traits\Toast;
use App\Models\Post;
use App\Models\User;
use App\Models\Tag;

new class extends Component {
    use Toast, WithFileUploads;

    public Post $post;

    #[Rule('required')]
    public string $title = '';

    #[Rule('required')]
    public string $body = '';

    #[Rule('sometimes')]
    public string $date = '';

    #[Rule('sometimes')]
    public ?int $author_id = null;

    #[Rule('sometimes')]
    public array $tags = [];

    public Collection $usersSearchable;
    public Collection $tagsSearchable;

    public function mount(): void
    {
        $this->fill($this->post);
        $this->tags = $this->post->tags->pluck('id')->all();
        $this->searchUsers();
        $this->searchTags();
    }

    public function with(): array
    {
        return [];
    }

    public function save(): void
    {
        $data = $this->validate();

        $this->post->update($data);

        $this->post->tags()->sync($this->tags);

        $this->success('Post updated with success.', redirectTo: '/posts');
    }

    public function searchUsers(string $value = ''): void
    {
        $selectedOption = User::where('id', $this->author_id)->get();
        $this->usersSearchable = User::query()
            ->where('name', 'like', "%$value%")
            ->take(5)
            ->orderBy('name')
            ->get()
            ->merge($selectedOption);
    }

    public function searchTags(string $value = ''): void
    {
        $selectedOption = Tag::whereIn('id', $this->tags)->get();
        $this->tagsSearchable = Tag::query()
            ->where('name', 'like', "%$value%")
            ->take(5)
            ->orderBy('name')
            ->get()
            ->merge($selectedOption);
    }
}; ?>

<div>
    <x-header title="Update Post" separator />
    <x-form wire:submit="save">
        <x-input label="Title" wire:model="title" />
        <x-datetime label="Date" wire:model="date" />
        <x-choices label="Author" wire:model="author_id" :options="$usersSearchable" search-function="searchUsers" single searchable />
        <x-choices label="Tags" wire:model="tags" :options="$tagsSearchable" debounce="300ms" search-function="searchTags" searchable />
        <x-editor wire:model="body" label="Body" hint="The great story" />

        <x-slot:actions>
            <x-button label="Cancel" link="/posts" />
            <x-button label="Save" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>
