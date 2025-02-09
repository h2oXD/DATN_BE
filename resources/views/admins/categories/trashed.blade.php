@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="card bg-white shadow">
            <div class="card-header">
                <h2 class="mb-0">Thùng rác</h2>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <a href="{{ route('admin.categories.index') }}" class="btn btn-info mb-3">Quay trở lại danh mục</a>

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
                            {{-- Hiển thị danh mục cha bị xóa cùng danh mục con của nó --}}
                            @foreach ($trashedParents as $parent)
                                <tr>
                                    <td><strong>{{ $parent->name }}</strong></td>
                                    <td>{{ $parent->slug }}</td>
                                    <td>
                                        <form action="{{ route('admin.categories.restore', $parent->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-warning">Khôi phục</button>
                                        </form>
                                        <form id="delete-category-{{ $parent->id }}" action="{{ route('admin.categories.forceDelete', $parent->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $parent->id }})">Xóa vĩnh viễn</button>
                                        </form>
                                    </td>
                                </tr>
                    
                                {{-- Hiển thị danh mục con của danh mục cha đã xóa --}}
                                @foreach ($parent->children as $child)
                                    <tr class="bg-light">
                                        <td style="padding-left: 40px;">-- {{ $child->name }}</td>
                                        <td>{{ $child->slug }}</td>
                                        <td>
                                            <form action="{{ route('admin.categories.restore', $child->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-warning">Khôi phục</button>
                                            </form>
                                            <form id="delete-category-{{ $child->id }}" action="{{ route('admin.categories.forceDelete', $child->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $child->id }})">Xóa vĩnh viễn</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                    
                            {{-- Hiển thị danh mục con bị xóa nhưng danh mục cha chưa bị xóa --}}
                            @foreach ($trashedChildren as $child)
                                <tr>
                                    <td>{{ $child->name }} (Danh mục con của: {{ $child->parent->name ?? 'Không xác định' }})</td>
                                    <td>{{ $child->slug }}</td>
                                    <td>
                                        <form action="{{ route('admin.categories.restore', $child->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-warning">Khôi phục</button>
                                        </form>
                                        <form id="delete-category-{{ $child->id }}" action="{{ route('admin.categories.forceDelete', $child->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $child->id }})">Xóa vĩnh viễn</button>
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
                        document.getElementById(`delete-category-${id}`).submit();
                    }
                });
            }
        </script>
    </div>
@endsection
