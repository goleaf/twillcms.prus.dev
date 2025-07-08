@props([
    'type' => 'success', // success, error, info
    'show' => true,
    'timeout' => 4000,
])

@php
    $colors = [
        'success' => 'bg-green-100 border-green-500 text-green-700 dark:bg-green-900 dark:border-green-700 dark:text-green-300',
        'error' => 'bg-red-100 border-red-500 text-red-700 dark:bg-red-900 dark:border-red-700 dark:text-red-300',
        'info' => 'bg-blue-100 border-blue-500 text-blue-700 dark:bg-blue-900 dark:border-blue-700 dark:text-blue-300',
        'warning' => 'bg-yellow-100 border-yellow-500 text-yellow-700 dark:bg-yellow-900 dark:border-yellow-700 dark:text-yellow-300'
    ][$type] ?? 'bg-gray-100 border-gray-500 text-gray-700 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300';
    $icon = [
        'success' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>',
        'error' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>',
        'info' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01" /></svg>',
    ];
@endphp

<div x-data="{ show: {{ $show ? 'true' : 'false' }} }" x-show="show" x-init="setTimeout(() => show = false, {{ $timeout }})" class="fixed top-6 right-6 z-50" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="flex items-center px-4 py-3 border-l-4 rounded shadow {{ $colors }}">
        {!! $icon[$type] !!}
        <div class="flex-1">
            {{ $slot }}
        </div>
        <button @click="show = false" class="ml-4 text-xl leading-none focus:outline-none" aria-label="Dismiss notification">&times;</button>
    </div>
</div> 