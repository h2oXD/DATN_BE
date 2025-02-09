@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="card bg-white shadow">
            <div class="card-header">
                <h2 class="mb-0">Thung rác</h2>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

               


                <a href="{{ route('categories.index') }}" class="btn btn-info mb-3">Quay trở lại danh mục</a>

                <div class="table-responsive border-0 overflow-y-hidden">
                    <table class="table mb-0 text-nowrap table-centered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th>Slug</th> <!-- Thêm cột slug -->
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr class="accordion-toggle collapsed" data-bs-toggle="collapse"
                                    data-bs-target="#collapse{{ $category->id }}">
                                    <td>
                                        <i class="fe fe-chevron-down fs-4 me-2"></i>
                                        {{ $category->name }}
                                    </td>
                                    <td>{{ $category->slug }}</td>
                                    <td>
                                        <span class="dropdown dropstart">
                                            <a class="btn-icon btn btn-ghost btn-sm rounded-circle" href="#"
                                                role="button" data-bs-toggle="dropdown">
                                                <i class="fe fe-more-vertical"></i>
                                            </a>
                                            <span class="dropdown-menu">
                                                <form action="{{ route('categories.restore', $category->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fe fe-refresh-ccw"></i> Khôi phục
                                                    </button>
                                                </form>
                                                <form id="delete-category-{{ $category->id }}"
                                                    action="{{ route('categories.forceDelete', $category->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="dropdown-item text-danger"
                                                        onclick="confirmDelete({{ $category->id }})">
                                                        <i class="fe fe-trash"></i> Xóa vĩnh viễn
                                                    </button>
                                                </form>
                                            </span>
                                        </span>
                                    </td>
                                </tr>

                                {{-- Hiển thị danh mục con nếu có --}}
                                @foreach ($category->children as $child)
                                    <tr class="collapse bg-light" id="collapse{{ $category->id }}">
                                        <td style="padding-left: 40px;">-- {{ $child->name }}</td>
                                        <td>{{ $child->slug }}</td>
                                        <td>
                                            <span class="dropdown dropstart">
                                                <a class="btn-icon btn btn-ghost btn-sm rounded-circle" href="#"
                                                    role="button" data-bs-toggle="dropdown">
                                                    <i class="fe fe-more-vertical"></i>
                                                </a>
                                                <span class="dropdown-menu">
                                                    <form action="{{ route('categories.restore', $child->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fe fe-refresh-ccw"></i> Khôi phục
                                                        </button>
                                                    </form>
                                                    <form id="delete-category-{{ $child->id }}"
                                                        action="{{ route('categories.forceDelete', $child->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="dropdown-item text-danger"
                                                            onclick="confirmDelete({{ $child->id }})">
                                                            <i class="fe fe-trash"></i> Xóa vĩnh viễn
                                                        </button>
                                                    </form>
                                                </span>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmDelete(id) {
                Swal.fire({
                    title: "Bạn có chắc chắn muốn xoá?",
                    text: "Hành động này không thể hoàn tác!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Xoá",
                    cancelButtonText: "Hủy"
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(`delete-category-${id}`).submit();
                    }
                });
            }
        </script>
    </div>
@endsection
