@props([
    'text',
    'position' => 'top', // top, right, bottom, left
    'dark' => false
])

@php
    $tooltipId = 'tooltip-' . uniqid();
@endphp
<div
    x-data="{ show: false }"
    x-on:mouseenter="show = true"
    x-on:mouseleave="show = false"
    x-on:focusin="show = true"
    x-on:focusout="show = false"
    x-on:keydown.escape.window="show = false"
    class="relative inline-block"
>
    <span tabindex="0" aria-describedby="{{ $tooltipId }}">
        {{ $slot }}
    </span>
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute z-50 px-2 py-1 text-sm font-medium {{ $dark ? 'bg-gray-900 text-white' : 'bg-white text-gray-900' }} rounded-md shadow-lg whitespace-nowrap"
        style="display: none;"
        role="tooltip"
        id="{{ $tooltipId }}"
        @class([
            'top-0 left-1/2 -translate-x-1/2 -translate-y-full mb-2' => $position === 'top',
            'top-1/2 left-full -translate-y-1/2 ml-2' => $position === 'right',
            'bottom-0 left-1/2 -translate-x-1/2 translate-y-full mt-2' => $position === 'bottom',
            'top-1/2 right-full -translate-y-1/2 mr-2' => $position === 'left',
        ])
    >
        {{ $text }}
        <div
            class="absolute w-2 h-2 transform rotate-45 {{ $dark ? 'bg-gray-900' : 'bg-white' }}"
            @class([
                'bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2' => $position === 'top',
                'top-1/2 -left-1 -translate-y-1/2' => $position === 'right',
                'top-0 left-1/2 -translate-x-1/2 -translate-y-1/2' => $position === 'bottom',
                'top-1/2 -right-1 -translate-y-1/2' => $position === 'left',
            ])
        ></div>
    </div>
</div> 