@extends('layouts.master')

@section('content')
    <div class="card m-3">
        <div class="card-header d-flex justify-content-between align-content-center">
            <div class="d-flex">
                <h2 class="m-0 mx-2">Danh sách thẻ</h2>
                <a href="{{ route('admin.tags.trash') }}" class="btn btn-danger">Thùng rác</a>
            </div>
            <div>
                <a href="{{ route('admin.tags.create') }}" class="btn btn-primary">Thêm mới</a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <table class="table table-bordered my-2">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Thẻ</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $tag)
                        <tr>
                            <td>{{ $tag->id }}</td>
                            <td>{{ $tag->name }}</td>
                            <td>
                                {{-- <span class="dropdown dropstart">
                                    <a class="btn-icon btn btn-ghost btn-sm rounded-circle" href="#" role="button"
                                        data-bs-toggle="dropdown" data-bs-offset="-20,20" aria-expanded="false">
                                        <i class="fe fe-more-vertical"></i>
                                    </a>
                                    <span class="dropdown-menu">
                                        <span class="dropdown-header">Settings</span>
                                        <a href="{{ route('admin.tags.show', $tag->id) }}" class="dropdown-item">
                                            <svg class="w-10 me-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>Xem</a>
                                        <a class="dropdown-item" href="{{ route('admin.tags.edit', $tag->id) }}">
                                            <svg class="w-10 me-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>

                                            Sửa
                                        </a>
                                        <a class="dropdown-item" href="#">
                                            <form action="{{ route('admin.tags.destroy', $tag->id) }}" method="POST"
                                                style="display:inline-block;" id="delete-tag-{{ $tag->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <a class="dropdown-item">
                                                    <svg class="w-10 me-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                    <button type="button"
                                                        class="btn btn-transparent btn-sm fs-5 ps-0 w-100 text-start"
                                                        onclick="confirmDelete({{ $tag->id }})">Xóa</button>
                                                </a>

                                            </form>
                                        </a>
                                    </span> --}}

                                {{-- Nút sửa (Chuyển hướng đến trang chỉnh sửa tag hiện tại) --}}
                                <a href="{{ route('admin.tags.edit', $tag->id) }}" class="btn btn-warning btn-sm">Sửa</a>

                                {{-- Nút xóa (Gửi yêu cầu xóa đến server) --}}
                                <form action="{{ route('admin.tags.destroy', $tag->id) }}" method="POST"
                                    style="display:inline-block;" id="delete-tag-{{ $tag->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="confirmDelete({{ $tag->id }})">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $items->links() }}
        </div>
    </div>
@endsection
@section('script')
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
                    document.getElementById(`delete-tag-${id}`).submit();
                }
            });
        }
    </script>
@endsection
