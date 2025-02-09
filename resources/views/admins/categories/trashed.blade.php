@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="card bg-white shadow">
            <div class="card-header">
                <h2 class="mb-0">Danh mục đã xoá</h2>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <a href="{{ route('categories.index') }}" class="btn btn-secondary mb-3">Quay lại danh sách</a>

                <div class="table-responsive border-0 overflow-y-hidden">
                    <table class="table mb-0 text-nowrap table-centered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
@endsection
