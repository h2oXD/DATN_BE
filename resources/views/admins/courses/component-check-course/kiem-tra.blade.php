<!-- Tab kiểm tra  -->
<div class="tab-pane fade" id="kiemtra" role="tabpanel" aria-labelledby="kiemtra-tab">
    <div class="ms-5">
        <h4 class="mt-3">Kiểm tra</h4>
        @if ($totalLessons < 4)
            <p class="text-danger mb-2 fw-bold"><i class="fe fe-x ms-1"></i>Tổng số lượng bài học ít hơn 4 bài</p>
        @else
            <p class="text-success mb-2 fw-bold"><i class="fe fe-check ms-1"></i>Tổng số lượng bài học đã đủ ({{ $totalLessons }}/4)</p>
        @endif
        @if ($totalVideoDurationMinutes < 30)
            <p class="text-danger mb-2 fw-bold"><i class="fe fe-x ms-1"></i>Tổng thời gian video ít hơn 30 phút ({{$totalVideoDurationMinutes}}/30 phút)</p>
        @else
            <p class="text-success mb-2 fw-bold"><i class="fe fe-check ms-1"></i>Tổng thời gian video đã đủ ({{ $totalVideoDurationMinutes }}/30 phút)</p>
        @endif
        @if ($course->thumbnail)
            <p class="text-success mb-2 fw-bold"><i class="fe fe-check ms-1"></i>Đã có ảnh bìa</p>
        @else
            <p class="text-success mb-2 fw-bold"><i class="fe fe-x ms-1"></i>Chưa có ảnh bìa</p>
        @endif
        @if ($course->video_preview)
            <p class="text-success mb-2 fw-bold"><i class="fe fe-check ms-1"></i>Đã có video quảng cáo</p>
        @else
            <p class="text-success mb-2 fw-bold"><i class="fe fe-x ms-1"></i>Chưa có video quảng cáo</p>
        @endif
        @if ($course->description)
            <p class="text-success mb-2 fw-bold"><i class="fe fe-check ms-1"></i>Đã có mô tả cho khoá học</p>
        @else
            <p class="text-success mb-2 fw-bold"><i class="fe fe-x ms-1"></i>Chưa có mô tả cho khoá học</p>
        @endif
        @if ($course->title)
            <p class="text-success mb-2 fw-bold"><i class="fe fe-check ms-1"></i>Đã có tiêu đề cho khoá học</p>
        @else
            <p class="text-success mb-2 fw-bold"><i class="fe fe-x ms-1"></i>Chưa có tiêu đề cho khoá học</p>
        @endif
    </div>
</div>
