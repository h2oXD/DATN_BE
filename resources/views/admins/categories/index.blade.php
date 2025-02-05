@extends('layouts.master')

@section('content')
    <div class="container">
        <h2>Danh sách danh mục</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('categories.create') }}" class="btn btn-primary mb-3">Thêm danh mục</a>

        <ul class="list-group">
            @foreach ($categories as $index => $category)
                @if ($category->parent_id === null) {{-- Chỉ hiển thị danh mục cha --}}
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <span>{{ $index + 1 }}. <strong>{{ $category->name }}</strong></span> {{-- STT và tên danh mục cha --}}
                            <div>
                                <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Xóa danh mục này?')">Xóa</button>
                                </form>
                            </div>
                        </div>
        
                        {{-- Nút dropdown hiển thị danh mục con --}}
                        @if ($category->children->count())
                            <button class="btn btn-info btn-sm toggle-subcategories">▼</button>
                            <ul class="list-group mt-2 d-none">
                                @foreach ($category->children as $child)
                                    <li class="list-group-item" style="padding-left: 30px;"> {{-- Căn thẳng hàng với danh mục cha --}}
                                        <div class="d-flex justify-content-between">
                                            <span>-- {{ $child->name }}</span> {{-- Tên danh mục con --}}
                                            <div>
                                                <a href="{{ route('categories.edit', $child->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                                                <form action="{{ route('categories.destroy', $child->id) }}" method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Xóa danh mục này?')">Xóa</button>
                                                </form>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <button class="btn btn-info btn-sm">Không có danh mục con</button>
                        @endif
                    </li>
                @endif
            @endforeach
        </ul>
        
        {{-- JavaScript để bật/tắt danh mục con --}}
        <script>
            document.querySelectorAll('.toggle-subcategories').forEach(button => {
                button.addEventListener('click', function() {
                    let subcategories = this.nextElementSibling;
                    subcategories.classList.toggle('d-none');
                    this.textContent = subcategories.classList.contains('d-none') ? '▼' : '▲';
                });
            });
        </script>
        
    </div>
@endsection
