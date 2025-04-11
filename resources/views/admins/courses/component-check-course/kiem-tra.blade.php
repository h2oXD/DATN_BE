<!-- Tab kiểm tra  -->
<div class="tab-pane fade" id="kiemtra" role="tabpanel" aria-labelledby="kiemtra-tab">
    <div class="ms-5">
        <h4 class="mt-3">Kiểm tra</h4>
        @if ($totalLessons < 4)
            <div class="d-flex align-items-center mb-2 text-danger">
                <i class="fe fe-x me-1"></i>
                <p class="m-0 fw-bold">Tổng số lượng bài học ít hơn 4 bài</p>
            </div>
        @else
            <div class="d-flex align-items-center mb-2 text-success">
                <i class="fe fe-check me-1"></i>
                <p class="m-0 fw-bold">Tổng số lượng bài học đã đủ
                    ({{ $totalLessons }}/4)</p>
            </div>
        @endif
        @if ($totalVideoDurationMinutes < 30)
            <div class="d-flex align-items-center mb-2 text-danger">
                <i class="fe fe-x me-1"></i>
                <p class="m-0 fw-bold">Tổng thời gian video ít hơn 30 phút
                    ({{ $totalVideoDurationMinutes }}/30
                    phút)</p>
            </div>
        @else
            <div class="d-flex align-items-center mb-2 text-success">
                <i class="fe fe-check me-1"></i>
                <p class="m-0 fw-bold">Tổng thời gian video đã đủ
                    ({{ $totalVideoDurationMinutes }}/30 phút)</p>
            </div>
        @endif
        @if ($course->thumbnail)
            <div class="d-flex align-items-center mb-2 text-success">
                <i class="fe fe-check me-1"></i>
                <p class="m-0 fw-bold">Đã có ảnh bìa</p>
            </div>
        @else
            <div class="d-flex align-items-center mb-2 text-danger">
                <i class="fe fe-x me-1"></i>
                <p class="m-0 fw-bold">Chưa có ảnh bìa</p>
            </div>
        @endif
        @if ($course->video_preview)
            <div class="d-flex align-items-center mb-2 text-success">
                <i class="fe fe-check me-1"></i>
                <p class="text-success m-0 fw-bold">Đã có video quảng cáo</p>
            </div>
        @else
            <div class="d-flex align-items-center mb-2 text-danger">
                <i class="fe fe-x me-1"></i>
                <p class="text-success m-0 fw-bold">Chưa có video quảng cáo</p>
            </div>
        @endif
        @if ($course->description)
            <div class="d-flex align-items-center mb-2 text-success">
                <i class="fe fe-check me-1"></i>
                <p class="text-success m-0 fw-bold">Đã có mô tả cho khoá học</p>
            </div>
        @else
            <div class="d-flex align-items-center mb-2 text-danger">
                <i class="fe fe-x me-1"></i>
                <p class="text-success m-0 fw-bold">Chưa có mô tả cho khoá học</p>
            </div>
        @endif
        @if ($course->title)
            <div class="d-flex align-items-center mb-2 text-success">
                <i class="fe fe-check me-1"></i>
                <p class="text-success m-0 fw-bold">Đã có tiêu đề cho khoá học</p>
            </div>
        @else
            <div class="d-flex align-items-center mb-2 text-danger">
                <i class="fe fe-x me-1"></i>
                <p class="text-success m-0 fw-bold">Chưa có tiêu đề cho khoá học</p>
            </div>
        @endif
    </div>
</div>
