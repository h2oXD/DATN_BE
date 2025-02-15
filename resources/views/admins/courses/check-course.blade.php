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
            <div class="card-body pb-1">
                <h1>{{ $course->title }}</h1>
                <ul class="nav nav-lb-tab border-bottom-0" id="tab" role="tablist">
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
                    <h3 class="m-3">Danh sách bài chờ duyệt</h3>
                </div>
                <div class="tab-pane fade" id="kiemtra" role="tabpanel" aria-labelledby="kiemtra-tab">
                    <h3 class="m-3">Kiem tra</h3>
                </div>
            </div>
        </div>
    </div>
@endsection
