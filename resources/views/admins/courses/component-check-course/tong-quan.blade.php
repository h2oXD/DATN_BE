<!-- Tab Approved -->
<div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
    <div class="card">
        <div class="card-body">
            <!-- Phan nay la tong quan-->
            <div class="container">
                <div class="row g-4">
                    <!-- Phần Video Preview + Mô Tả (70%) -->
                    <div class="col-md-8">
                        <!-- Video Preview -->
                        <div class="d-flex gap-3 flex-row">
                            <div class="w-50">
                                <label class="fw-bold" for="">Video quảng cáo</label>
                                <video controls class="w-100 rounded"
                                    src="{{ Storage::url($course->video_preview) }}"></video>
                            </div>
                            <div class="w-50">
                                <label class="fw-bold" for="">Ảnh bìa</label>
                                <img src="{{ Storage::url($course->thumbnail) }}" class="w-100 rounded"
                                    alt="">
                            </div>
                        </div>
                        <!-- Mô Tả -->
                        <div class="card mt-2 shadow-none rounded p-3">
                            <!-- Tiêu đề Mô tả -->
                            <h4 class="card-title h4 fw-bold">{{$course->title}}</h4>
                            <label class="fw-bold" for="">Nội dung chính</label>
                            <p class="mb-3">{{$course->primary_content}}</p>
                            <!-- Nội dung Mô tả với gạch chân -->
                            <div class=" pb-13 mb-3">
                                <label class="fw-bold" for="">Mô tả</label>
                                <p class="card-text text-muted">{{ $course->description }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Phần Tổng Quan Khóa Học (30%) -->
                    <div class="col-md-4">
                        <div class="card shadow-none">
                            <div class="card-body">
                                <!-- Tiêu đề Tổng quan khóa học -->
                                <h2 class="card-title h4 fw-bold mb-3 border-bottom pb-3">Tổng quan
                                    khóa học</h2>
                                <!-- Nội dung Tổng quan khóa học -->
                                <ul class="list-unstyled text-muted">
                                    <li class="d-flex justify-content-between mb-3">
                                        <span><strong>Thời gian video:</strong></span>
                                        <span>30 Phút</span>
                                    </li>
                                    <li class="d-flex justify-content-between mb-3">
                                        <span><strong>Bài giảng:</strong></span>
                                        <span>{{ $totalLessons }}</span>
                                    </li>
                                    <li class="d-flex justify-content-between mb-3">
                                        <span><strong>Trình độ:</strong></span>
                                        <span>{{ $course->level }}</span>
                                    </li>
                                    <li class="d-flex justify-content-between mb-3">
                                        <span class="fw-bold">Giá: </span>
                                        @if ($course->is_free)
                                            <td class="border-end">Miễn phí</td>
                                        @elseif($course->price_sale)
                                            <td class="border-end">
                                                <span
                                                    style="text-decoration: line-through; color: gray;">{{ $course->price_regular }}
                                                    VNĐ</span>
                                                {{ $course->price_sale }} VNĐ
                                            </td>
                                        @else
                                            <td class="border-end">{{ $course->price_regular }} VNĐ</td>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>