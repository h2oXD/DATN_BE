@extends('layouts.master')

@section('content')
    <div class="container my-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-content-center">
                <h2>Chi tiết Tag</h2>
            </div>
            <div class="card-body">
                <p><strong>ID:</strong> {{ $item->id }}</p>
                <p><strong>Tên Tag:</strong> {{ $item->name }}</p>
                <a href="{{ route('tags.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </div>
    </div>
@endsection
