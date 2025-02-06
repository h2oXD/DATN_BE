@extends('layouts.master')

@section('title')
    Danh sách phiếu giảm giá
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-content-center">
            <h2 class="m-0">Danh sách phiếu giảm giá</h2>
            <a href="{{ route('vouchers.create') }}" class="btn btn-primary">Thêm mới phiếu giảm giá</a>
        </div>
        <div class="card-body">
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
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Tên</th>
                            <th scope="col">Mã giảm giá</th>
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
                                <td>
                                    @if ($vouchers->type === 'percent')
                                        <span class="">%</span>
                                    @elseif ($vouchers->type === 'fix_amount')
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
                                    @elseif ($vouchers->count === '0')
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
                                    <a href="{{ route('vouchers.show', $vouchers->id) }}">
                                        <svg class="w-10" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('vouchers.edit', $vouchers->id) }}">
                                        <svg class="w-10 me-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </a>
                                    {{-- <form action="{{ route('vouchers.destroy', $vouchers->id) }}" method="POST"
                                        style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                                    </form> --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{-- {{ $users->links() }} --}}
            </div>
        </div>
    </div>
@endsection
