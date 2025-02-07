@extends('layouts.master')

@section('title')
    Chi tiết phiếu giảm giá
@endsection

@section('content')
    <div class="container my-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-content-center">
                <h2>Chi tiết phiếu giảm giá</h2>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3"><strong>ID:</strong></div>
                    <div class="col-md-9">{{ $item->id }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Tên:</strong></div>
                    <div class="col-md-9">{{ $item->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Mã giảm giá:</strong></div>
                    <div class="col-md-9">{{ $item->code }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Nội dung:</strong></div>
                    <div class="col-md-9">{{ $item->description }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Loại giảm giá:</strong></div>
                    <div class="col-md-9">
                        @if ($item->type === 'percent')
                            <span class="">%</span>
                        @elseif ($item->type === 'fix_amount')
                            <span class="">Giá</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Số % giảm:</strong></div>
                    <div class="col-md-9">
                        @if ($item->discount_percent)
                            <span class="">{{ $item->discount_percent }}%</span>
                        @else
                            <span class="">Không có</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Số tiền giảm:</strong></div>
                    <div class="col-md-9">
                        @if ($item->discount_amount)
                            <span class="">{{ $item->discount_amount }} VND</span>
                        @else
                            <span class="">Không có</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Ngày bắt đầu:</strong></div>
                    <div class="col-md-9">{{ $item->start_time }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Ngày hết hạn:</strong></div>
                    <div class="col-md-9">{{ $item->end_time }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Số lượng:</strong></div>
                    <div class="col-md-9">
                        @if ($item->count)
                            <span class="">{{ $item->count }}</span>
                        @elseif ($item->count === '0')
                            <span class="">Hết</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Trạng thái:</strong></div>
                    <div class="col-md-9">
                        @if ($item->is_active)
                            <span class="">Hoạt động</span>
                        @else
                            <span class="">Khóa</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Ngày tạo:</strong></div>
                    <div class="col-md-9">{{ $item->created_at }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Ngày cập nhật:</strong></div>
                    <div class="col-md-9">{{ $item->updated_at }}</div>
                </div>
                <div class="text-end">
                    <a href="{{ route('vouchers.index') }}" class="btn btn-secondary">Quay lại</a>
                </div>
            </div>
        </div>
    </div>
@endsection
