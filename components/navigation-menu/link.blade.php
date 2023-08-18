@props([
    'active' => false,
])

<a
    :data-active="@js($active) ? '' : undefined"
    :aria-current="@js($active) ? 'page' : undefined"
    data-zladix-collection-item
    {{ $attributes }}
>
    {{ $slot }}
</a>
