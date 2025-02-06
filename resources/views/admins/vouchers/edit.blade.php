@extends('layouts.master')

@section('title')
    Sửa phiếu giảm giá
@endsection

@section('content')
    <h1>Sửa phiếu giảm giá</h1>

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

    <div class="container">
        <form method="POST" action="{{ route('vouchers.update', $item->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3 row">
                <label for="name" class="col-4 col-form-label">Tên</label>
                <div class="col-8">
                    <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $item->name) }}" required />
                </div>
            </div>
            <div class="mb-3 row">
                <label for="code" class="col-4 col-form-label">Mã giảm giá</label>
                <div class="col-8">
                    <input type="text" class="form-control" name="code" id="code" value="{{ old('code', $item->code) }}" required />
                </div>
            </div>
            <div class="mb-3 row">
                <label for="description" class="col-4 col-form-label">Nội dung</label>
                <div class="col-8">
                    <input type="text" class="form-control" name="description" id="description" value="{{ old('description', $item->description) }}" />
                </div>
            </div>
            <div class="mb-3 row">
                <label for="type" class="col-4 col-form-label">Loại giảm giá</label>
                <div class="col-8">
                    <select name="type" class="form-select text-dark" required>
                        <option value="percent" {{ $item->type === 'percent' ? 'selected' : '' }}>Phần trăm</option>
                        <option value="fix_amount" {{ $item->type === 'fix_amount' ? 'selected' : '' }}>Giá tiền</option>
                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="discount_percent" class="col-4 col-form-label">Số % giảm</label>
                <div class="col-8">
                    <input type="number" class="form-control" name="discount_percent" id="discount_percent" min="0" value="{{ old('discount_percent', $item->discount_percent) }}"/>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="discount_amount" class="col-4 col-form-label">Số tiền giảm</label>
                <div class="col-8">
                    <input type="number" class="form-control" name="discount_amount" id="discount_amount" min="0" value="{{ old('discount_amount', $item->discount_amount) }}"/>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="start_time" class="col-4 col-form-label">Ngày bắt đầu</label>
                <div class="col-8">
                    <input type="datetime-local" class="form-control" name="start_time" id="start_time" required value="{{ old('start_time', $item->start_time) }}"/>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="end_time" class="col-4 col-form-label">Ngày kết thúc</label>
                <div class="col-8">
                    <input type="datetime-local" class="form-control" name="end_time" id="end_time" required value="{{ old('end_time', $item->end_time) }}"/>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="count" class="col-4 col-form-label">Số lượng</label>
                <div class="col-8">
                    <input type="number" class="form-control" name="count" id="count" required min="0" value="{{ old('count', $item->count) }}"/>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="is_active" class="col-4 col-form-label">Trạng thái</label>
                <div class="col-8">
                    <select name="is_active" class="form-select text-dark" required>
                        <option value="1" {{ $item->is_active == '1' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="0" {{ $item->is_active == '0' ? 'selected' : '' }}>Khóa</option>
                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="offset-sm-4 col-sm-8">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </div>
        </form>
    </div>
@endsection
