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