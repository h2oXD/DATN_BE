@extends('layouts.master')

@section('title')
    Chi tiết lịch sử
@endsection

@section('content')
    <div class="container my-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-content-center">
                <h2>Chi tiết lịch sử</h2>
            </div>
            <div class="card-body">

                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h4>Thông tin Người Dùng</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Họ tên:</strong></div>
                            <div class="col-md-8">{{ $item->user->name }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Email:</strong></div>
                            <div class="col-md-8">{{ $item->user->email }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Số điện thoại:</strong></div>
                            <div class="col-md-8">{{ $item->user->phone_number }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4>Thông tin Phiếu Giảm Giá</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Tên phiếu giảm giá:</strong></div>
                            <div class="col-md-8">{{ $item->voucher->name }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Mã giảm giá:</strong></div>
                            <div class="col-md-8">{{ $item->voucher->code }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Nội dung:</strong></div>
                            <div class="col-md-8">{{ $item->voucher->description }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Loại giảm giá:</strong></div>
                            <div class="col-md-8">
                                @if ($item->voucher->type === 'percent')
                                    Giảm theo phần trăm (%)
                                @elseif ($item->voucher->type === 'fix_amount')
                                    Giảm theo số tiền cụ thể
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Số % / Số tiền giảm:</strong></div>
                            <div class="col-md-8">
                                @if ($item->voucher->type === 'percent')
                                    {{ $item->voucher->discount_price }} %
                                @elseif ($item->voucher->type === 'fix_amount')
                                    {{ number_format($item->voucher->discount_price) }} VND
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h4>Thông tin Khóa Học</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Tên khóa học:</strong></div>
                            <div class="col-md-8">{{ $item->course->title }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Giá gốc:</strong></div>
                            <div class="col-md-8">{{ number_format($item->course->price_regular) }} VND</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Giá đã giảm:</strong></div>
                            <div class="col-md-8">{{ number_format($item->course->price_sale) }} VND</div>
                        </div>
                    </div>
                </div>

                <div class="text-center my-4">
                    <h5><strong>Ngày sử dụng:</strong> {{ \Carbon\Carbon::parse($item->time_used)->format('d/m/Y H:i:s') }}</h5>
                </div>

                <div class="text-end">
                    <a href="{{ route('admin.voucher-use.index') }}" class="btn btn-secondary">Quay lại</a>
                </div>

            </div>
        </div>
    </div>
@endsection
