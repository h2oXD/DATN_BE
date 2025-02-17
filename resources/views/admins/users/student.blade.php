@extends('layouts.master')

@section('title')
    Danh sách học viên
@endsection

@section('content')
    <div class="card m-3">
        <div class="card-header d-flex justify-content-between align-content-center">
            <h2 class="m-0">Danh sách học viên</h2>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Thêm mới người dùng</a>
        </div>
        <div class="card-body p-0">
            <form method="GET" action="{{ route('admin.students.index') }}" class="row gx-3 m-2">
                <div class="col-lg-8 col-12 mb-2">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm"
                        value="{{ request('search') }}">
                </div>
                <div class="col-lg-2 col-12 mb-2">
                    <button type="submit" class="btn btn-info ms-2">Tìm kiếm</button>
                </div>
            </form>
            @if (session()->has('success') && session()->get('success'))
                <div class="alert alert-info">
                    Thao tác thành công!
                </div>
            @endif

            @if (session()->has('success') && !session()->get('success'))
                <div class="alert alert-danger">
                    {{ session()->get('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table mb-3 text-nowrap table-hover table-centered">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Tên</th>
                            <th scope="col">Vai trò</th>
                            <th scope="col">Email</th>
                            <th scope="col">Ngày tham gia</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center flex-row gap-2">
                                        <div class="position-relative">
                                            <img src="{{ Storage::url($user->profile_picture) }}" alt="avatar"
                                                class="rounded-circle avatar-md">
                                            <a href="#" class="position-absolute mt-5 ms-n4">
                                                <span class="status bg-success"></span>
                                            </a>
                                        </div>
                                        <h5 class="mb-0">{{ $user->name }}</h5>
                                    </div>
                                </td>
                                <td>
                                    @foreach ($user->roles as $role)
                                        @if ($role->name == 'student')
                                            <span class="badge bg-success">Học viên</span>
                                        @endif
                                    @endforeach
                                </td>
                                
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at }}</td>
                                <td>
                                    <span class="dropdown dropstart">
                                        <a class="btn-icon btn btn-ghost btn-sm rounded-circle" href="#"
                                            role="button" data-bs-toggle="dropdown" data-bs-offset="-20,20"
                                            aria-expanded="false">
                                            <i class="fe fe-more-vertical"></i>
                                        </a>
                                        <span class="dropdown-menu">
                                            <span class="dropdown-header">Settings</span>
                                            <a href="{{ route('admin.users.show', $user->id) }}" class="dropdown-item">
                                                <svg class="w-10 me-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>Xem
                                            </a>
                                            <a class="dropdown-item" href="{{ route('admin.users.edit', $user->id) }}">
                                                <svg class="w-10 me-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                                Sửa
                                            </a>
                                            <a href="javascript:void(0)" class="dropdown-item"
                                                onclick="confirmDelete({{ $user->id }})">
                                                <svg class="w-10 me-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                                Xóa
                                            </a>
                                            <form id="delete-tag-{{ $user->id }}"
                                                action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                                style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </span>
                                    </span>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $users->links() }}
            </div>
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
