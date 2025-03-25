@extends('layouts.master')

@section('content')
    <div class="container mt-3">
        <div class="card bg-white shadow">
            <div class="card-header">
                <h2 class="mb-0">Danh sách Banner</h2>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <a href="{{ route('admin.banners.create') }}" class="btn btn-primary mb-3">Thêm Banner</a>

                <div class="table-responsive border-0 overflow-y-hidden">
                    <table class="table mb-0 text-nowrap table-centered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Banner</th>
                                <th>Tiêu đề</th>
                                <th>Đường dẫn</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($banners as $banner)
                                <tr>
                                    <td>
                                        <img src="{{ asset('storage/' . $banner->image) }}" width="120"
                                            class="img-thumbnail">
                                    </td>
                                    <td>{{ $banner->title }}</td>
                                    <td>
                                        @if ($banner->link)
                                            <a href="{{ $banner->link }}" target="_blank">{{ $banner->link }}</a>
                                        @else
                                            <span class="text-muted">Không có</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($banner->status)
                                            <span class="badge bg-success">Hoạt động</span>
                                        @else
                                            <span class="badge bg-danger">Không hoạt động</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-warning btn-sm"
                                            href="{{ route('admin.banners.edit', $banner->id) }}">
                                            <i class="fe fe-edit"></i> Sửa
                                        </a>
                                        <form id="delete-banner-{{ $banner->id }}"
                                            action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="confirmDelete({{ $banner->id }})">
                                                <i class="fe fe-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
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
                        document.getElementById(`delete-banner-${id}`).submit();
                    }
                });
            }
        </script>
    </div>
@endsection
