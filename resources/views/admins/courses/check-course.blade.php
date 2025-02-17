{{-- @extends('layouts.master')
@section('title')
    Kiểm duyệt khoá học - {{ $course->title }}
@endsection

@section('content')
    <div class="m-3">
        <div class="card">
            <div class="card-body pb-1">
                <h1>ABC</h1>
                <ul class="nav nav-lb-tab border-bottom-0" id="tab" role="tablist">
                    <li class="nav-item my-1" role="presentation">
                        <a class="nav-link p-0 active" id="courses-tab" data-bs-toggle="pill" href="#courses" role="tab"
                            aria-controls="courses" aria-selected="true">All</a>
                    </li>
                    <li class="nav-item my-1" role="presentation">
                        <a class="nav-link p-0 m-" id="approved-tab" data-bs-toggle="pill" href="#approved" role="tab"
                            aria-controls="approved" aria-selected="false" tabindex="-1">Approved</a>
                    </li>
                    <li class="nav-item my-1" role="presentation">
                        <a class="nav-link p-0 m-" id="pending-tab" data-bs-toggle="pill" href="#pending" role="tab"
                            aria-controls="pending" aria-selected="false" tabindex="-1">Pending</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card mt-2" id="tabContent">
            <div class="tab-pane fade show" id="courses" role="tabpanel" aria-labelledby="courses-tab">
                <h1>Course</h1>
            </div>
            <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                <h1>approved</h1>
            </div>
            <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <h1>pending</h1>
            </div>
        </div>
    </div>
@endsection --}}

@extends('layouts.master')

@section('title')
    Kiểm duyệt khoá học - {{ $course->title }}
@endsection

@section('content')
    <div class="m-3">
        <div class="card">
            <div class="card-body pb-1 pt-3 px-3">
                <li class="list-group-item bg-transparent pb-2">
                    <div class="row align-items-center">
                        <div class="col">
                            <a href="#">
                                <div class="d-flex align-items-center flex-row gap-3">
                                    @if ($course->thumbnail)
                                        <img src="{{ Storage::url($course->thumbnail) }}" alt=""
                                            class="avatar-lg rounded">
                                    @else
                                        <img src="/avatar-1.jpg" alt="" class="avatar-lg rounded">
                                    @endif

                                    <div class="text-body">
                                        <p class="mb-0">
                                            <span class="fw-bold mb-0 h5">{{ $course->user->name }}</span>
                                            <span class="ms-1 fs-6">{{ $course->user->email }}</span>
                                        </p>
                                        <span class="fs-6">
                                            <span class="ms-1">Thời gian gửi: {{ $course->submited_at }}</span>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-auto text-center">
                            <button class="btn btn-outline-primary btn-sm">Chấp nhận</button>
                            <button class="btn btn-outline-danger btn-sm">Từ chối</button>
                        </div>
                    </div>
                </li>
                <ul class="nav nav-lb-tab border-bottom-0 pb-2 pt-2 border-top" id="tab" role="tablist">
                    <li class="nav-item my-1" role="presentation">
                        <a class="nav-link p-0 active" id="courses-tab" data-bs-toggle="pill" href="#courses" role="tab"
                            aria-controls="courses" aria-selected="true">Nội dung các bài</a>
                    </li>
                    <li class="nav-item my-1" role="presentation">
                        <a class="nav-link p-0" id="approved-tab" data-bs-toggle="pill" href="#approved" role="tab"
                            aria-controls="approved" aria-selected="false">Tổng quan</a>
                    </li>
                    <li class="nav-item my-1" role="presentation">
                        <a class="nav-link p-0" id="pending-tab" data-bs-toggle="pill" href="#pending" role="tab"
                            aria-controls="pending" aria-selected="false">Thông tin</a>
                    </li>
                    <li class="nav-item my-1" role="presentation">
                        <a class="nav-link p-0" id="kiemtra-tab" data-bs-toggle="pill" href="#kiemtra" role="tab"
                            aria-controls="kiemtra" aria-selected="false">Kiểm tra</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card mt-2" id="tabContent">
            <div class="tab-content">
                <!-- Tab All -->
                <div class="tab-pane fade show active" id="courses" role="tabpanel" aria-labelledby="courses-tab">
                    @if ($course->sections->isEmpty())
                        <p class="m-3 text-muted">Chưa có chương nào</p>
                    @else
                        @foreach ($course->sections as $section)
                            <div class="card m-3">
                                <div class="card-header">
                                    <h4>Chương {{ $loop->iteration }}: {{ $section->title }}</h4>
                                </div>
                                <div class="card-body pt-2">
                                    @if ($section->lessons->isEmpty())
                                        <p class="text-muted">Chưa có bài nào</p>
                                    @else
                                        <ul class="list-group list-group-flush" id="section{{ $section->id }}">
                                            @foreach ($section->lessons as $lesson)
                                                <li class="list-group-item px-0">
                                                    <!-- Toggle -->
                                                    <a class="h5 mb-0 d-flex align-items-center" data-bs-toggle="collapse"
                                                        href="#lesson{{ $lesson->id }}" aria-expanded="false"
                                                        aria-controls="lesson{{ $lesson->id }}">
                                                        <div class="me-auto">Bài {{ $loop->iteration }}:
                                                            {{ $lesson->title }}</div>
                                                        <span class="chevron-arrow ms-4">
                                                            <i class="fe fe-chevron-down fs-4"></i>
                                                        </span>
                                                    </a>

                                                    <!-- Collapse -->
                                                    <div class="collapse mt-2" id="lesson{{ $lesson->id }}"
                                                        data-bs-parent="#section{{ $section->id }}">
                                                        <div class="p-3 ">
                                                            <!-- Tài liệu -->
                                                            <h6 class="mb-1">Tài liệu:</h6>
                                                            <ul class="mb-2">
                                                                @forelse ($lesson->documents as $document)
                                                                    <li><a href="{{ $document->url }}"
                                                                            target="_blank">{{ $document->name }}</a></li>
                                                                @empty
                                                                    <li class="text-muted">Không có tài liệu</li>
                                                                @endforelse
                                                            </ul>

                                                            <!-- Video -->
                                                            <h6 class="mb-1">Video:</h6>
                                                            <ul class="mb-2">
                                                                @forelse ($lesson->videos as $video)
                                                                    <li><a href="{{ $video->url }}"
                                                                            target="_blank">{{ $video->title }}</a></li>
                                                                @empty
                                                                    <li class="text-muted">Không có video</li>
                                                                @endforelse
                                                            </ul>

                                                            <!-- Code mẫu -->
                                                            <h6 class="mb-1">Code mẫu:</h6>
                                                            <ul class="mb-2">
                                                                @forelse ($lesson->codings as $coding)
                                                                    <li><a href="{{ $coding->url }}"
                                                                            target="_blank">{{ $coding->title }}</a></li>
                                                                @empty
                                                                    <li class="text-muted">Không có bài code</li>
                                                                @endforelse
                                                            </ul>

                                                            <!-- Quiz -->
                                                            <h6 class="mb-1">Bài Quiz:</h6>
                                                            <ul class="mb-0">
                                                                @forelse ($lesson->quizzes as $quiz)
                                                                    <li>{{ $quiz->title }}</li>
                                                                @empty
                                                                    <li class="text-muted">Không có bài quiz</li>
                                                                @endforelse
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Tab Approved -->
                <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                    <div class="card">
                        <div class="card-body">
                            <!-- Phan nay la tong quan-->

                            <div class="bg-light py-5">
                                <div class="container">
                                    <div class="row g-4">
                                        <!-- Phần Video Preview + Mô Tả (70%) -->
                                        <div class="col-md-8">
                                            <!-- Video Preview -->
                                            <div id="video-container" class="card mb-4" style="display: none;">
                                                <div class="card-body p-0">
                                                    <video id="video-preview" class="w-100 rounded-top" controls>
                                                        <source src="video_url.mp4" type="video/mp4">
                                                        Trình duyệt của bạn không hỗ trợ video.
                                                    </video>
                                                </div>
                                            </div>
                                            <!-- Mô Tả -->
                                            <div class="card">
                                                <div class="card-body">
                                                    <!-- Tiêu đề Mô tả -->
                                                    <h2 class="card-title h4 fw-bold mb-3">MÔ TẢ</h2>
                                                    <!-- Nội dung Mô tả với gạch chân -->
                                                    <div class="border-bottom pb-3 mb-3">
                                                        <p class="card-text text-muted">Mô tả nội dung khóa học sẽ hiển thị
                                                            ở đây...</p>
                                                    </div>
                                                    <!-- Phần bổ sung (nếu cần) -->
                                                    <p class="card-text text-muted">Thêm thông tin bổ sung (nếu có).</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Phần Tổng Quan Khóa Học (30%) -->
                                        <div class="col-md-4">
                                            <div class="card">
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
                                                            <span><strong>Bài kiểm tra:</strong></span>
                                                            <span>2</span>
                                                        </li>
                                                        <li class="d-flex justify-content-between mb-3">
                                                            <span><strong>Trình độ:</strong></span>
                                                            <span>Sơ cấp</span>
                                                        </li>
                                                        <li class="d-flex justify-content-between mb-3">
                                                            <span><strong>Học viên:</strong></span>
                                                            <span>0</span>
                                                        </li>
                                                        <li class="d-flex justify-content-between mb-3">
                                                            <span><strong>Price:</strong></span>
                                                            <span class="badge bg-success">Free</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bootstrap JS và Popper.js -->
                            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
                            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

                            <script>
                                // Kiểm tra nếu có video hay không
                                const video = document.getElementById('video-preview');
                                if (video && video.querySelector('source').getAttribute('src') !== "") {
                                    document.getElementById('video-container').style.display = 'block';
                                }
                            </script>
                        </div>
                    </div>
                </div>

                <!-- Tab Pending -->
                <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="m-3">Danh sách các khóa học chờ duyệt</h3>
                        </div>
                        <div class="card-body">
                            
                        </div>
                    </div>
                </div>

                <!-- Tab kiểm tra  -->
                <div class="tab-pane fade" id="kiemtra" role="tabpanel" aria-labelledby="kiemtra-tab">
                    <h3 class="m-3">Kiem tra</h3>
                </div>
            </div>
        </div>
    </div>
@endsection
