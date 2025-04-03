@extends('layouts.master')

@section('title')
    Chi tiết danh mục
@endsection

@section('content')
    <div class="container my-5">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            {{-- Header --}}
            <div class="card-header bg-primary text-white py-3 px-4">
                <h3 class="mb-0 d-flex align-items-center">
                    <i class="lucide lucide-folder-open me-2"></i> Chi tiết danh mục
                </h3>
            </div>

            {{-- Body --}}
            <div class="card-body bg-light p-4">
                {{-- Thông tin chính --}}
                <div class="mb-4">
                    <h5 class="fw-bold mb-2 text-secondary">Tên danh mục</h5>
                    <p class="mb-0 fs-5">{{ $category->name }}</p>
                </div>

                <div class="mb-4">
                    <h5 class="fw-bold mb-2 text-secondary">Danh mục cha</h5>
                    <p class="mb-0 fs-5">
                        {{ $category->parent ? $category->parent->name : 'Không có' }}
                    </p>
                </div>

                {{-- Danh mục con --}}
                <div class="mb-4">
                    <h5 class="fw-bold mb-3 text-secondary">Danh mục con</h5>
                    @if ($category->children->count() > 0)
                        <ul class="list-group">
                            @foreach ($category->children as $child)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $child->name }}
                                    <a href="{{ route('admin.categories.show', $child->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="lucide lucide-eye"></i> Xem
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted fst-italic">Không có danh mục con nào.</p>
                    @endif
                </div>

                {{-- Nút quay lại --}}
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary px-4 shadow-sm">
                        <i class="lucide lucide-arrow-left me-2"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>

        {{-- Lucide Icons --}}
        <script src="https://unpkg.com/lucide@latest"></script>
        <script>
            lucide.createIcons();
        </script>
    </div>
@endsection
