@extends('layouts.master')

@section('content')
    <div class="card shadow-sm border-0 rounded-4 m-3">
        <div class="card-header bg-white border-0 py-3 px-4 rounded-top-4 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-semibold text-primary">Danh sách thẻ</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.tags.create') }}" class="btn btn-success rounded">
                    <i class="fe fe-plus me-1"></i> Thêm thẻ
                </a>
                <a href="{{ route('admin.tags.trash') }}" class="btn btn-outline-secondary rounded">
                    Thùng rác
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            @if (session('success'))
                <div class="alert alert-success m-3">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 10%">#</th>
                            <th style="width: 60%">Tên thẻ</th>
                            <th style="width: 30%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $tag)
                            <tr>
                                <td class="fw-bold">{{ $tag->id }}</td>
                                <td>{{ $tag->name }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.tags.show', $tag->id) }}"
                                            class="btn btn-warning btn-sm rounded">
                                            Xem
                                        </a>
                                        <a href="{{ route('admin.tags.edit', $tag->id) }}"
                                            class="btn btn-warning btn-sm rounded">
                                            Sửa
                                        </a>
                                        <button type="submit" onclick="confirmDelete({{ $tag->id }})"
                                            class="btn btn-danger btn-sm rounded">
                                            Xóa
                                        </button>
                                        <form action="{{ route('admin.tags.destroy', $tag->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Không có thẻ nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end p-3">
                {{ $items->links() }}
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
