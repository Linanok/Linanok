@props([
    'icon',
    'theme',
])

@php
    $label = __("filament-panels::layout.actions.theme_switcher.{$theme}.label");
@endphp

<button
    x-on:click="{{ $attributes->get('x-on:click') }}"
    aria-label="{{ $label }}"
    type="button"
    x-tooltip="{
        content: @js($label),
        theme: $store.theme,
    }"
    class="fi-theme-switcher-btn flex items-center gap-2 rounded-md px-3 py-2 outline-none transition duration-75 hover:bg-gray-50 focus-visible:bg-gray-50 dark:hover:bg-white/5 dark:focus-visible:bg-white/5 border border-gray-300 dark:border-gray-600"
    x-bind:class="
        theme === @js($theme)
            ? 'fi-active bg-gray-50 text-primary-500 dark:bg-white/5 dark:text-primary-400 border-primary-500 dark:border-primary-400'
            : 'text-gray-400 hover:text-gray-500 focus-visible:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 dark:focus-visible:text-gray-400'
    "
>
    <x-filament::icon
        :icon="$icon"
        class="h-5 w-5"
    />
    <span class="font-medium">
        {{ Str::ucfirst($theme) }}
    </span>
</button>
