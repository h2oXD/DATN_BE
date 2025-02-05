@extends('layouts.master')

@section('title')
    Chi tiết phiếu giảm giá
@endsection

@section('content')
    <h1>Chi tiết phiếu giảm giá</h1>

    <p><strong>ID:</strong> {{ $item->id }}</p>
    <p><strong>Tên:</strong> {{ $item->name }}</p>
    <p><strong>Mã giảm giá:</strong> {{ $item->code }}</p>
    <p><strong>Nội dung:</strong> {{ $item->description }}</p>
    <p><strong>Loại giảm giá:</strong> 
        @if ($item->type === "percent")
            <span class="">%</span>
        @elseif ($item->type === "fix_amount")
            <span class="">Giá</span>
        @endif
    </p>
    <p><strong>Số % giảm:</strong> 
        @if ($item->discount_percent)
            <span class="">{{ $item->discount_percent }}%</span>
        @else
            <span class="">Không có</span>
        @endif
    </p>
    <p><strong>Số tiền giảm:</strong> 
        @if ($item->discount_amount)
            <span class="">{{ $item->discount_amount }} VND</span>
        @else
            <span class="">Không có</span>
        @endif
    </p>
    <p><strong>Ngày bắt đầu:</strong> {{ $item->start_time }}</p>
    <p><strong>Ngày hết hạn:</strong> {{ $item->end_time }}</p>
    <p><strong>Số lượng:</strong> 
        @if ($item->count)
            <span class="">{{ $item->count }}</span>
        @elseif ($item->count === "0")
            <span class="">Hết</span>
        @endif
    </p>
    <p><strong>Trạng thái:</strong> 
        @if ($item->is_active)
            <span class="">Hoạt động</span>
        @else
            <span class="">Khóa</span>
        @endif
    </p>
    <p><strong>Ngày tạo:</strong> {{ $item->created_at }}</p>
    <p><strong>Ngày cập nhật:</strong> {{ $item->updated_at }}</p>

    <a href="{{ route('vouchers.index') }}" class="btn btn-secondary">Quay lại</a>
@endsection
