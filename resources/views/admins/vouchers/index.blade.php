@extends('layouts.master')

@section('title')
    Danh sách phiếu giảm giá
@endsection

@section('content')
    <h1>Danh sách phiếu giảm giá</h1>

    @if (session()->has('success') && !session()->get('success'))
        <div class="alert alert-danger">
            {{ session()->get('error') }}
        </div>
    @endif

    @if (session()->has('success') && session()->get('success'))
        <div class="alert alert-info">
            Thao tác thành công!
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

    <a href="{{ route('vouchers.create') }}" class="btn btn-primary">Thêm mới phiếu giảm giá</a>

    <div class="table-responsive mt-4">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Tên</th>
                    <th scope="col">Mã giảm giá</th>
                    <th scope="col">Nội dung</th>
                    <th scope="col">Loại giảm giá</th>
                    <th scope="col">Giảm %</th>
                    <th scope="col">Giảm giá</th>
                    <th scope="col">Số lượng</th>
                    <th scope="col">Trạng thái</th>
                    <th scope="col">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $vouchers)
                    <tr>
                        <td>{{ $vouchers->id }}</td>
                        <td>{{ $vouchers->name }}</td>
                        <td>{{ $vouchers->code }}</td>
                        <td>{{ $vouchers->description }}</td>
                        <td>
                            @if ($vouchers->type === "percent")
                                <span class="">%</span>
                            @elseif ($vouchers->type === "fix_amount")
                                <span class="">Giá</span>
                            @endif
                        </td>
                        <td>
                            @if ($vouchers->discount_percent)
                                <span class="badge bg-primary">{{ $vouchers->discount_percent }}%</span>
                            @else
                                <span class="badge bg-danger">Không có</span>
                            @endif
                        </td>
                        <td>
                            @if ($vouchers->discount_amount)
                                <span class="badge bg-primary">{{ $vouchers->discount_amount }} VND</span>
                            @else
                                <span class="badge bg-danger">Không có</span>
                            @endif
                        </td>
                        <td>
                            @if ($vouchers->count)
                                <span class="">{{ $vouchers->count }}</span>
                            @elseif ($vouchers->count === "0")
                                <span class="badge bg-danger">Hết</span>
                            @endif
                        </td>
                        <td>
                            @if ($vouchers->is_active)
                                <span class="badge bg-primary">Hoạt động</span>
                            @else
                                <span class="badge bg-danger">Khóa</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('vouchers.show', $vouchers->id) }}" class="btn btn-info">Xem</a>
                            <a href="{{ route('vouchers.edit', $vouchers->id) }}" class="btn btn-warning">Sửa</a>
                            <form action="{{ route('vouchers.destroy', $vouchers->id) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{-- {{ $users->links() }} --}}
    </div>
@endsection
