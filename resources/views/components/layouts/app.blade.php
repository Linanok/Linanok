<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Page Title' }}</title>

    @livewireStyles
    @filamentStyles
    <link href="{{ asset('css/filament/filament/app.css') }}" rel="stylesheet" data-navigate-track/>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet"/>

    @vite('resources/css/app.css')
</head>
<body
    class="fi-body min-h-screen bg-gray-50 font-normal text-gray-950 antialiased dark:bg-gray-950 dark:text-white flex flex-col">
<header
    class="fi-simple-header flex h-16 w-full items-center border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
    <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 w-full">
        <div class="flex flex-1">
            <div class="flex items-center">
                <span class="font-bold text-xl">{{ $title ?? 'Page Title' }}</span>
            </div>
        </div>
        <div
            class="flex gap-2"
            x-data="{
                        theme: localStorage.getItem('theme') || 'system',
                        init: function () {
                            $dispatch('theme-changed', this.theme)
                        },
                        toggleTheme: function(newTheme) {
                            this.theme = newTheme
                            localStorage.setItem('theme', newTheme)
                            $dispatch('theme-changed', newTheme)
                        }
                    }">
            <x-theme-switcher-button
                icon="heroicon-m-computer-desktop"
                theme="system"
                x-on:click="toggleTheme('system')"
            ></x-theme-switcher-button>

            <x-theme-switcher-button
                icon="heroicon-m-moon"
                theme="dark"
                x-on:click="toggleTheme('dark')"
            ></x-theme-switcher-button>

            <x-theme-switcher-button
                icon="heroicon-m-sun"
                theme="light"
                x-on:click="toggleTheme('light')"
            ></x-theme-switcher-button>
        </div>
    </div>
</header>

<main class="flex-1 p-4 sm:p-6 lg:p-8">
    {{ $slot }}
</main>

@livewireScripts
@livewire('notifications')
@filamentScripts()

<script src="{{ asset('js/filament/filament/echo.js') }}"></script>
<script src="{{ asset('js/filament/filament/app.js') }}"></script>

@vite('resources/js/app.js')

@include('components.copyright')
</body>
</html>
