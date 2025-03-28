@extends('layouts.master')

@section('title')
    Chi tiết phiếu giảm giá
@endsection

@section('content')
    <div class="container my-5">
        <div class="card">
            <div class="card-header">
                <h2 class="m-0">Chi tiết phiếu giảm giá</h2>
            </div>
            <div class="card-body">
                <!-- ID và Tên -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">ID</label>
                        <input type="text" class="form-control" value="{{ $item->id }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tên</label>
                        <input type="text" class="form-control" value="{{ $item->name }}" readonly>
                    </div>
                </div>

                <!-- Mã giảm giá và Nội dung -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Mã giảm giá</label>
                        <input type="text" class="form-control" value="{{ $item->code }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nội dung</label>
                        <textarea class="form-control" rows="2" readonly>{{ $item->description }}</textarea>
                    </div>
                </div>

                <!-- Loại giảm giá và Số % / Số tiền giảm -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Loại giảm giá</label>
                        <input type="text" class="form-control"
                            value="@if ($item->type === 'percent') % @elseif ($item->type === 'fix_amount') Giá @endif"
                            readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Số % / Số tiền giảm</label>
                        <input type="text" class="form-control"
                            value="@if ($item->discount_price) {{ $item->discount_price }} 
                               @if ($item->type === 'percent') % @elseif ($item->type === 'fix_amount') Giá @endif 
                               @endif"
                            readonly>
                    </div>
                </div>

                <!-- Ngày bắt đầu và Ngày hết hạn -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Ngày bắt đầu</label>
                        <input type="text" class="form-control"
                            value="{{ \Carbon\Carbon::parse($item->start_time)->format('d/m/Y H:i:s') }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ngày hết hạn</label>
                        <input type="text" class="form-control"
                            value="{{ \Carbon\Carbon::parse($item->end_time)->format('d/m/Y H:i:s') }}" readonly>
                    </div>
                </div>

                <!-- Số lượng và Trạng thái -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Số lượng</label>
                        <input type="text" class="form-control"
                            value="@if ($item->count) {{ $item->count }} @elseif ($item->count === '0') Hết @endif"
                            readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Trạng thái</label>
                        <input type="text"
                            class="form-control 
                        @if ($item->is_active) bg-success text-white 
                        @else bg-danger text-white @endif"
                            value="@if ($item->is_active) Hoạt động @else Khóa @endif" readonly>
                    </div>
                </div>

                <!-- Ngày tạo và Ngày cập nhật -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Ngày tạo</label>
                        <input type="text" class="form-control"
                            value="{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i:s') }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ngày cập nhật</label>
                        <input type="text" class="form-control"
                            value="{{ \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y H:i:s') }}" readonly>
                    </div>
                </div>

                <!-- Button quay lại -->
                <div class="mt-4">
                    <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">Quay lại danh sách</a>
                </div>
            </div>
        </div>
    </div>
@endsection
