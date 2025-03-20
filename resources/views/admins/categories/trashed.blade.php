@extends('layouts.master')

@section('content')
    <div class="container my-5">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            {{-- Header --}}
            <div class="card-header bg-danger text-white py-3 px-4 d-flex justify-content-between align-items-center">
                <h3 class="mb-0 d-flex align-items-center">
                    <i class="lucide lucide-trash-2 me-2"></i> Thùng rác danh mục
                </h3>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-light btn-sm">
                    <i class="lucide lucide-arrow-left"></i> Quay lại danh mục
                </a>
            </div>

            {{-- Body --}}
            <div class="card-body bg-light p-4">

                {{-- Alerts --}}
                @if (session('success'))
                    <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
                @endif

                {{-- Table --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start">Danh mục</th>
                                <th>Slug</th>
                                <th width="220px">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Danh mục cha bị xoá cùng danh mục con --}}
                            @forelse ($trashedParents as $parent)
                                <tr class="fw-bold">
                                    <td class="text-start">
                                        <i class="lucide lucide-folder me-1"></i> {{ $parent->name }}
                                    </td>
                                    <td>{{ $parent->slug }}</td>
                                    <td>
                                        <form action="{{ route('admin.categories.restore', $parent->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning">
                                                <i class="lucide lucide-rotate-ccw me-1"></i> Khôi phục
                                            </button>
                                        </form>
                                        <form id="delete-category-{{ $parent->id }}"
                                            action="{{ route('admin.categories.forceDelete', $parent->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="confirmDelete({{ $parent->id }})">
                                                <i class="lucide lucide-trash-2 me-1"></i> Xóa vĩnh viễn
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- Danh mục con --}}
                                @foreach ($parent->children as $child)
                                    <tr class="bg-light text-muted">
                                        <td class="text-start ps-5">
                                            <i class="lucide lucide-subtitles me-1"></i> {{ $child->name }}
                                        </td>
                                        <td>{{ $child->slug }}</td>
                                        <td>
                                            <form action="{{ route('admin.categories.restore', $child->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning">
                                                    <i class="lucide lucide-rotate-ccw me-1"></i> Khôi phục
                                                </button>
                                            </form>
                                            <form id="delete-category-{{ $child->id }}"
                                                action="{{ route('admin.categories.forceDelete', $child->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="confirmDelete({{ $child->id }})">
                                                    <i class="lucide lucide-trash-2 me-1"></i> Xóa vĩnh viễn
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="3">
                                        <span class="text-muted fst-italic">Không có danh mục cha nào trong thùng
                                            rác.</span>
                                    </td>
                                </tr>
                            @endforelse

                            {{-- Danh mục con bị xoá riêng lẻ --}}
                            @forelse ($trashedChildren as $child)
                                <tr>
                                    <td class="text-start">
                                        <i class="lucide lucide-subtitles me-1"></i>
                                        {{ $child->name }}
                                        <span class="badge bg-secondary ms-2">Con của:
                                            {{ $child->parent->name ?? 'Không xác định' }}</span>
                                    </td>
                                    <td>{{ $child->slug }}</td>
                                    <td>
                                        <form action="{{ route('admin.categories.restore', $child->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning">
                                                <i class="lucide lucide-rotate-ccw me-1"></i> Khôi phục
                                            </button>
                                        </form>
                                        <form id="delete-category-{{ $child->id }}"
                                            action="{{ route('admin.categories.forceDelete', $child->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="confirmDelete({{ $child->id }})">
                                                <i class="lucide lucide-trash-2 me-1"></i> Xóa vĩnh viễn
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">
                                        <span class="text-muted fst-italic">Không có danh mục con riêng lẻ nào trong thùng
                                            rác.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- SweetAlert2 + Lucide Icons --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://unpkg.com/lucide@latest"></script>
        <script>
            lucide.createIcons();

            function confirmDelete(id) {
                Swal.fire({
                    title: "Bạn có chắc muốn xoá vĩnh viễn?",
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
