@extends('layouts.master')

@section('title')
    Sửa phiếu giảm giá
@endsection

@section('content')

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

    <div class="card">
        <div class="card-header">
            <h2 class="m-0">Cập nhật phiếu giảm giá</h2>
        </div>
        <div class="card-body">
            <form method="POST" class="row" action="{{ route('vouchers.update', $item->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="col-lg-6 mb-2 col-12">
                    <label for="name" class="form-label">Tên</label>
                    <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $item->name) }}"
                        required />
                </div>
                <div class="col-lg-6 mb-2 col-12">
                    <label for="code" class="form-label">Mã giảm giá</label>
                    <input type="text" class="form-control" name="code" id="code" value="{{ old('code', $item->code) }}"
                        required />
                </div>

                <div class="col-lg-6 mb-2 col-12">
                    <label for="description" class="form-label">Nội dung</label>
                    <input type="text" class="form-control" name="description" id="description"
                    value="{{ old('description', $item->description) }}" />
                </div>

                <div class="col-lg-6 mb-2 col-12">
                    <label for="type" class="form-label">Loại giảm giá</label>
                    <select name="type" class="form-select text-dark" required>
                        <option value="percent" {{ $item->type === 'percent' ? 'selected' : '' }}>Phần trăm</option>
                        <option value="fix_amount" {{ $item->type === 'fix_amount' ? 'selected' : '' }}>Giá tiền</option>
                    </select>
                </div>

                <div class="col-lg-6 mb-2 col-12">
                    <label for="discount_percent" class="form-label">Số % giảm</label>
                    <input type="number" class="form-control" name="discount_percent" id="discount_percent"
                        min="0" value="{{ old('discount_percent', $item->discount_percent) }}"/>
                </div>

                <div class="col-lg-6 mb-2 col-12">
                    <label for="discount_amount" class="form-label">Số tiền giảm</label>
                    <input type="number" class="form-control" name="discount_amount" id="discount_amount" min="0" 
                    value="{{ old('discount_amount', $item->discount_amount) }}"/>
                </div>

                <div class="col-lg-6 mb-2 col-12">
                    <label for="start_time" class="form-label">Ngày bắt đầu</label>
                    <input type="datetime-local" class="form-control" name="start_time" id="start_time" required value="{{ old('start_time', $item->start_time) }}"/>
                </div>

                <div class="col-lg-6 mb-2 col-12">
                    <label for="end_time" class="form-label">Ngày kết thúc</label>
                    <input type="datetime-local" class="form-control" name="end_time" id="end_time" required value="{{ old('end_time', $item->end_time) }}"/>
                </div>

                <div class="col-lg-6 mb-2 col-12">
                    <label for="count" class="form-label">Số lượng</label>
                    <input type="number" class="form-control" name="count" id="count" required min="0" value="{{ old('count', $item->count) }}"/>
                </div>

                <div class="col-lg-6 mb-2 col-12">
                    <label for="is_active" class="form-label">Trạng thái</label>
                    <select name="is_active" class="form-select text-dark" required>
                        <option value="1" {{ $item->is_active == '1' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="0" {{ $item->is_active == '0' ? 'selected' : '' }}>Khóa</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                    <a href="{{ route('vouchers.index') }}" class="btn btn-secondary">Quay lại</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const typeSelect = document.querySelector('select[name="type"]');
            const discountPercentInput = document.querySelector('input[name="discount_percent"]');
            const discountAmountInput = document.querySelector('input[name="discount_amount"]');

            function toggleDiscountFields() {
                if (typeSelect.value === "percent") {
                    discountPercentInput.removeAttribute("disabled");
                    discountAmountInput.setAttribute("disabled", "disabled");
                    discountAmountInput.value = "";
                } else {
                    discountAmountInput.removeAttribute("disabled");
                    discountPercentInput.setAttribute("disabled", "disabled");
                    discountPercentInput.value = "";
                }
            }

            // Gọi hàm khi trang tải xong để đảm bảo đúng trạng thái ban đầu
            toggleDiscountFields();

            // Gán sự kiện thay đổi giá trị
            typeSelect.addEventListener("change", toggleDiscountFields);
        });
    </script>
@endsection
