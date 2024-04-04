@props(['left' => ''])

<div class="lg:grid grid-cols-12">
    @unless(empty($left))
    <div class="col-span-3">
        {{ $left }}
    </div>
    @endunless
    <div class="col-span-9 grid gap-3">
        {{ $slot }}
    </div>
</div>
