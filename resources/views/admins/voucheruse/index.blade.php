@extends('layouts.master')

@section('title')
    Lịch sử phiếu giảm giá đã dùng
@endsection

@section('content')
    <div class="card m-3 ">
        <div class="card-header d-flex justify-content-between align-content-center">
            <h2 class="m-0">Lịch sử phiếu giảm giá đã dùng</h2>
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
                            <th scope="col">ID</th>
                            <th scope="col">Tên phiếu giảm giá</th>
                            <th scope="col">Mã giảm giá</th>
                            <th scope="col">Loại giảm giá</th>
                            <th scope="col">Số % / tiền giảm</th>
                            <th scope="col">Tên người dùng</th>
                            <th scope="col">Email</th>
                            <th scope="col">Số điện thoại</th>
                            <th scope="col">Tên khóa học</th>
                            <th scope="col">Giá khóa học</th>
                            <th scope="col">Giá đã giảm</th>
                            <th scope="col">Ngày sử dụng</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->voucher_name }}</td>
                                <td>{{ $item->code }}</td>
                                <td>
                                    @if ($item->type === 'percent')
                                        <span class="">%</span>
                                    @elseif ($item->type === 'fix_amount')
                                        <span class="">Giá</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->type === 'percent' && $item->discount_price)
                                        <span class="">{{ $item->discount_price }}%</span>
                                    @elseif ($item->type === 'fix_amount' && $item->discount_price)
                                        <span class="">{{ number_format($item->discount_price) }} VND</span>
                                    @endif
                                </td>
                                <td>{{ $item->user_name }}</td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->phone_number }}</td>
                                <td>{{ $item->title }}</td>
                                <td>{{ number_format($item->price_regular) }} VND</td>
                                <td>{{ number_format($item->price_sale) }} VND</td>
                                <td>{{ \Carbon\Carbon::parse($item->time_used)->format('d/m/Y H:i:s') }}</td>
                                <td>
                                    <span class="dropdown dropstart">
                                        <a class="btn-icon btn btn-ghost btn-sm rounded-circle" href="#"
                                            role="button" data-bs-toggle="dropdown" data-bs-offset="-20,20"
                                            aria-expanded="false">
                                            <i class="fe fe-more-vertical"></i>
                                        </a>
                                        <span class="dropdown-menu">
                                            <span class="dropdown-header">Hành động</span>
                                            <a href="{{ route('admin.voucher-use.show', $item->id) }}" class="dropdown-item">
                                                <svg class="w-10 me-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>Xem chi tiết
                                            </a>
                                            <form action="{{ route('admin.voucher-use.destroy', $item->id) }}" method="POST"
                                                style="display:inline-block;" id="delete-voucher-{{ $item->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <a class="dropdown-item">
                                                    <svg class="w-10 me-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                    <button type="button"
                                                        class="btn btn-transparent btn-sm fs-5 ps-0 w-100 text-start"
                                                        onclick="confirmDelete({{ $item->id }})">Xóa</button>
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
