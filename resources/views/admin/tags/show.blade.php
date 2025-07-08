<h1 class="text-2xl font-bold mb-6">{{ __('Tag Details') }}</h1>
<div class="mb-4">
    <span class="font-semibold">{{ __('Id') }}:</span> {{ $tag->id }}
</div>
<div class="mb-4">
    <span class="font-semibold">{{ __('Name') }}:</span> {{ $tag->name }}
</div>
<div class="mb-4">
    <span class="font-semibold">{{ __('Color') }}:</span> {{ $tag->color }}
</div>
<div class="mb-4">
    <span class="font-semibold">{{ __('Description') }}:</span> {{ $tag->description ?? '-' }}
</div>
<div class="mb-4">
    <span class="font-semibold">{{ __('Usage Count') }}:</span> {{ $tag->usage_count ?? 0 }}
</div>
<div class="mb-4">
    <span class="font-semibold">{{ __('Featured') }}:</span> {{ $tag->is_featured ? __('Yes') : __('No') }}
</div> 