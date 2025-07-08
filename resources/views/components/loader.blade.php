<div role="status" aria-live="polite" class="flex flex-col items-center justify-center py-8">
    <svg class="animate-spin h-8 w-8 text-blue-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
    </svg>
    <span class="text-gray-700 dark:text-gray-200 text-sm mt-2">{{ $slot ?? __('Loading...') }}</span>
</div> 