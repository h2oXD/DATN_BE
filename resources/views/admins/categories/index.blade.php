@extends('layouts.master')

@section('title')
    Danh sách danh mục
@endsection

@section('content')
    <div class="container my-5">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header d-flex justify-content-between align-content-center">
                <h2 class="mb-0">Danh sách danh mục</h2>

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
                    <form method="GET" action=" " class="row gx-3 mb-1">
                        <div class="col-lg-8 col-12 mb-2">
                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm danh mục"
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-lg-2 col-12 mb-2">
                            <select name="category" class="form-select ms-2 text-dark">
                                <option value="">Tất cả danh mục</option>
                                @foreach ($parentCategory as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-12 mb-2">
                            <button type="submit" class="btn btn-info ms-2">Tìm kiếm</button>
                        </div>
                    </form>
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
                                    <tr class="accordion-toggle collapsed" data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $category->id }}">
                                        <td>
                                            <i class="lucide lucide-chevron-down me-2 text-muted"></i>
                                            <strong>{{ $category->name }}</strong>
                                        </td>
                                        <td>{{ $category->slug }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.categories.edit', $category->id) }}"
                                                class="btn btn-sm btn-outline-warning me-1">
                                                <i class="lucide lucide-pencil"></i> Sửa
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="confirmDelete({{ $category->id }})">
                                                <i class="lucide lucide-trash"></i> Xoá
                                            </button>
                                            <form id="delete-category-{{ $category->id }}"
                                                action="{{ route('admin.categories.destroy', $category->id) }}"
                                                method="POST" class="d-none">
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
                                                <a href="{{ route('admin.categories.edit', $child->id) }}"
                                                    class="btn btn-sm btn-outline-warning me-1">
                                                    <i class="lucide lucide-pencil"></i> Sửa
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmDelete({{ $child->id }})">
                                                    <i class="lucide lucide-trash"></i> Xoá
                                                </button>
                                                <form id="delete-category-{{ $child->id }}"
                                                    action="{{ route('admin.categories.destroy', $child->id) }}"
                                                    method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                            {{-- @if ($noResults)
                                <p>Không tìm thấy kết quả tìm kiếm.</p>
                            @endif --}}
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
