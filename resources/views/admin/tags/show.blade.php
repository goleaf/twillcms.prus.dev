<h1 class="text-2xl font-bold mb-6">Tag Details</h1>
<div class="mb-4">
    <span class="font-semibold">Id:</span> {{ $tag->id }}
</div>
<div class="mb-4">
    <span class="font-semibold">Name:</span> {{ $tag->name }}
</div>
<div class="mb-4">
    <span class="font-semibold">Color:</span> {{ $tag->color }}
</div>
<div class="mb-4">
    <span class="font-semibold">Description:</span> {{ $tag->description ?? '-' }}
</div>
<div class="mb-4">
    <span class="font-semibold">Usage Count:</span> {{ $tag->usage_count ?? 0 }}
</div>
<div class="mb-4">
    <span class="font-semibold">Featured:</span> {{ $article->is_featured ? 'Yes' : 'No' }}
</div> 