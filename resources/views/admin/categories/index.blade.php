@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Categories</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-success mb-3">Add Category</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Slug</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories ?? [] as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->title }}</td>
                    <td>{{ $category->slug }}</td>
                    <td>
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 