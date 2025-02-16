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
                            @if ($courses->isEmpty())
                                <p class="m-3 text-muted">Không có bài nào đang chờ duyệt</p>
                            @else
                                <ul class="list-group m-3">
                                    @foreach ($courses as $item)
                                        <li class="list-group-item d-flex align-items-center">
                                            <img src="{{ $item->thumbnail ? asset('storage/' . $item->thumbnail) : asset('default-thumbnail.jpg') }}"
                                                alt="Thumbnail" class="me-3"
                                                style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                            <div>
                                                <h5 class="mb-1">{{ $item->title }}</h5>
                                                <small class="text-muted">
                                                    Giảng viên: {{ optional($item->user)->name ?? 'Chưa có' }} |
                                                    Danh mục: {{ optional($item->category)->name ?? 'Chưa có' }}
                                                </small>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
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
