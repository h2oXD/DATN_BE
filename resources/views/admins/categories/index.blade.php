@extends('layouts.master')

@section('content')
    <div class="container my-5">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-bold text-primary">Danh sách danh mục</h2>
            <div>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-success me-2">
                    <i class="lucide lucide-plus"></i> Thêm danh mục
                </a>
                <a href="{{ route('admin.categories.trashed') }}" class="btn btn-outline-secondary">
                    <i class="lucide lucide-trash-2"></i> Thùng rác
                </a>
            </div>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
                </div>
            @endif

            <div class="table-responsive rounded-3 overflow-hidden">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Danh mục</th>
                            <th scope="col">Slug</th>
                            <th scope="col" class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            @if ($category->parent_id === null)
                                <tr class="accordion-toggle collapsed" data-bs-toggle="collapse" data-bs-target="#collapse{{ $category->id }}">
                                    <td>
                                        <i class="lucide lucide-chevron-down me-2 text-muted"></i>
                                        <strong>{{ $category->name }}</strong>
                                    </td>
                                    <td>{{ $category->slug }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-outline-warning me-1">
                                            <i class="lucide lucide-pencil"></i> Sửa
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $category->id }})">
                                            <i class="lucide lucide-trash"></i> Xoá
                                        </button>
                                        <form id="delete-category-{{ $category->id }}" action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>

                                @foreach ($category->children as $child)
                                    <tr class="collapse" id="collapse{{ $category->id }}">
                                        <td class="ps-5 text-muted">↳ {{ $child->name }}</td>
                                        <td>{{ $child->slug }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.categories.edit', $child->id) }}" class="btn btn-sm btn-outline-warning me-1">
                                                <i class="lucide lucide-pencil"></i> Sửa
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $child->id }})">
                                                <i class="lucide lucide-trash"></i> Xoá
                                            </button>
                                            <form id="delete-category-{{ $child->id }}" action="{{ route('admin.categories.destroy', $child->id) }}" method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
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

    {{-- SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

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
