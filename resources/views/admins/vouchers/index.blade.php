@extends('layouts.master')

@section('title')
    Danh sách phiếu giảm giá
@endsection

@section('content')
    <div class="container my-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-content-center">
                <h2 class="m-0">Danh sách phiếu giảm giá</h2>

                <a href="{{ route('vouchers.create') }}" class="btn btn-primary">Thêm mới phiếu giảm giá</a>
            </div>
            <div class="card-body p-0">
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
                    <table class="table mb-3 text-nowrap table-hover table-centered">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Id</th>
                                <th scope="col">Tên</th>
                                <th scope="col">Mã giảm giá</th>
                                <th scope="col">Loại giảm giá</th>
                                <th scope="col">Giảm %/giá</th>
                                <th scope="col">Số lượng</th>
                                <th scope="col">Trạng thái</th>
                                <th scope="col"></th>
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
                                        @if ($vouchers->type === 'percent' && $vouchers->discount_percent)
                                            <span class="">{{ $vouchers->discount_percent }}%</span>
                                        @elseif ($vouchers->type === 'fix_amount' && $vouchers->discount_amount)
                                            <span class="">{{ number_format($vouchers->discount_amount) }} VND</span>
                                        @else
                                            <span class="badge bg-warning">Không có</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($vouchers->count)
                                            <span class="">{{ $vouchers->count }}</span>
                                        @elseif ($vouchers->count == '0')
                                            <span class="badge bg-warning">Hết</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($vouchers->is_active)
                                            <span class="badge bg-success">Hoạt động</span>
                                        @else
                                            <span class="badge bg-danger">Khóa</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="dropdown dropstart">
                                            <a class="btn-icon btn btn-ghost btn-sm rounded-circle" href="#"
                                                role="button" data-bs-toggle="dropdown" data-bs-offset="-20,20"
                                                aria-expanded="false">
                                                <i class="fe fe-more-vertical"></i>
                                            </a>
                                            <span class="dropdown-menu">
                                                <span class="dropdown-header">Hành động</span>
                                                <a href="{{ route('vouchers.show', $vouchers->id) }}" class="dropdown-item">
                                                    <svg class="w-10 me-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                    </svg>Xem
                                                </a>
                                                <a class="dropdown-item"
                                                    href="{{ route('vouchers.edit', $vouchers->id) }}">
                                                    <svg class="w-10 me-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                    </svg>
                                                    Sửa
                                                </a>
                                                <form action="{{ route('vouchers.destroy', $vouchers->id) }}"
                                                    method="POST" style="display:inline-block;"
                                                    id="delete-voucher-{{ $vouchers->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a class="dropdown-item">
                                                        <svg class="w-10 me-2" xmlns="http://www.w3.org/2000/svg"
                                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor" class="size-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                        </svg>
                                                        <button type="button"
                                                        class="btn btn-transparent btn-sm fs-5 ps-0 w-100 text-start"
                                                        onclick="confirmDelete({{ $vouchers->id }})">Xóa</button>
                                                    </a>
                                                    
                                                </form>
                                                
                                            </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $items->links() }}
                </div>
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
                    document.getElementById(`delete-voucher-${id}`).submit();
                }
            });
        }
    </script>
@endsection
