@extends('layouts.master')

@section('content')
<div class="container">
    <h2>Chi tiết danh mục</h2>
    <p><strong>Tên danh mục:</strong> {{ $category->name }}</p>
    <p><strong>Danh mục cha:</strong> {{ $category->parent ? $category->parent->name : 'Không có' }}</p>

    <h4>Danh mục con</h4>
    <ul>
        @foreach ($category->children as $child)
            <li>{{ $child->name }}</li>
        @endforeach
    </ul>

    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Quay lại</a>
</div>


@endsection