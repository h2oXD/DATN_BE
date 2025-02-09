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

    {{-- Đoạn này dùng để thông báo lỗi thành một khối --}}
    {{-- @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif --}}

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
                         />
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-lg-6 mb-2 col-12">
                    <label for="code" class="form-label">Mã giảm giá</label>
                    <input type="text" class="form-control" name="code" id="code" value="{{ old('code', $item->code) }}"
                         />
                    @error('code')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-lg-6 mb-2 col-12">
                    <label for="type" class="form-label">Loại giảm giá</label>
                    <select name="type" class="form-select text-dark" >
                        <option value="percent" {{ $item->type === 'percent' ? 'selected' : '' }}>Phần trăm</option>
                        <option value="fix_amount" {{ $item->type === 'fix_amount' ? 'selected' : '' }}>Giá tiền</option>
                    </select>
                    @error('type')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-lg-6 mb-2 col-12">
                    <label for="discount_price" class="form-label">Số % / Số tiền giảm</label>
                    <input type="number" class="form-control" name="discount_price" id="discount_price"
                        min="0" value="{{ old('discount_price', $item->discount_price) }}"/>
                    @error('discount_price')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-lg-6 mb-2 col-12">
                    <label for="start_time" class="form-label">Ngày bắt đầu</label>
                    <input type="datetime-local" class="form-control" name="start_time" id="start_time"  value="{{ old('start_time', $item->start_time) }}"/>
                    @error('start_time')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-lg-6 mb-2 col-12">
                    <label for="end_time" class="form-label">Ngày kết thúc</label>
                    <input type="datetime-local" class="form-control" name="end_time" id="end_time"  value="{{ old('end_time', $item->end_time) }}"/>
                    @error('end_time')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-lg-6 mb-2 col-12">
                    <label for="count" class="form-label">Số lượng</label>
                    <input type="number" class="form-control" name="count" id="count" value="{{ old('count', $item->count) }}"/>
                    @error('count')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-lg-6 mb-2 col-12">
                    <label for="is_active" class="form-label">Trạng thái</label>
                    <select name="is_active" class="form-select text-dark" >
                        <option value="1" {{ $item->is_active == '1' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="0" {{ $item->is_active == '0' ? 'selected' : '' }}>Khóa</option>
                    </select>
                    @error('is_active')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="my-3 col-12">
                    <label for="description" class="form-label">Nội dung</label>
                    <textarea name="description" id="description">{{ old('description', $item->description) }}</textarea>
                    @error('description')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                    <a href="{{ route('vouchers.index') }}" class="btn btn-secondary">Quay lại</a>
                </div>
            </form>
        </div>
    </div>

    {{-- <script>
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
    </script> --}}
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/7.6.0/tinymce.min.js"
        integrity="sha512-/4EpSbZW47rO/cUIb0AMRs/xWwE8pyOLf8eiDWQ6sQash5RP1Cl8Zi2aqa4QEufjeqnzTK8CLZWX7J5ZjLcc1Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        tinymce.init({
            selector: 'textarea#description'
        });
    </script>
@endsection