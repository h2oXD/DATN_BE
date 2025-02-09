@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="card bg-white shadow">
            <div class="card-header">
                <h2 class="mb-0">Danh sách danh mục</h2>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mb-3">Thêm danh mục</a>
                <a href="{{ route('admin.categories.trashed') }}" class="btn btn-primary mb-3">Trash</a>

                <div class="table-responsive border-0 overflow-y-hidden">
                    <table class="table mb-0 text-nowrap table-centered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th>Slug</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                @if ($category->parent_id === null)
                                    <tr class="accordion-toggle collapsed" data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $category->id }}">
                                        <td>
                                            <i class="fe fe-chevron-down fs-4 me-2"></i>
                                            {{ $category->name }}
                                        </td>
                                        <td>{{ $category->slug }}</td>
                                        <td>
                                            <a class="btn btn-warning btn-sm" href="{{ route('admin.categories.edit', $category->id) }}">
                                                <i class="fe fe-edit"></i> Sửa
                                            </a>
                                            <form id="delete-category-{{ $category->id }}"
                                                  action="{{ route('admin.categories.destroy', $category->id) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="confirmDelete({{ $category->id }})">
                                                    <i class="fe fe-trash"></i> Xóa
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    @foreach ($category->children as $child)
                                        <tr class="collapse bg-light" id="collapse{{ $category->id }}">
                                            <td style="padding-left: 40px;">-- {{ $child->name }}</td>
                                            <td>{{ $child->slug }}</td>
                                            <td>
                                                <a class="btn btn-warning btn-sm" href="{{ route('admin.categories.edit', $child->id) }}">
                                                    <i class="fe fe-edit"></i> Sửa
                                                </a>
                                                <form id="delete-category-{{ $child->id }}"
                                                      action="{{ route('admin.categories.destroy', $child->id) }}"
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete({{ $child->id }})">
                                                        <i class="fe fe-trash"></i> Xóa
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
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
