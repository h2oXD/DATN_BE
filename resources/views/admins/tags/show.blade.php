@extends('layouts.master')

@section('content')
    <div class="container">
        <h2>Chi tiết Tag</h2>
        <p><strong>ID:</strong> {{ $tag->id }}</p>
        <p><strong>Tên Tag:</strong> {{ $tag->name }}</p>
        <a href="{{ route('tags.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>
@endsection
