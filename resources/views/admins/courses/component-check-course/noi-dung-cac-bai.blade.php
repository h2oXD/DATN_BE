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
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <b>Bài {{ $loop->iteration }}:</b>
                                        @if ($lesson->type == 'document')
                                            <i class="fe fe-file-text"></i>
                                        @endif
                                        @if ($lesson->type == 'quiz')
                                            <i class="fe fe-help-circle"></i>
                                        @endif
                                        @if ($lesson->type == 'video')
                                            <i class="fe fe-video"></i>
                                        @endif
                                        @if ($lesson->type == 'coding')
                                            <i class="fe fe-code"></i>
                                        @endif
                                        {{ $lesson->title }}
                                    </div>
                                    <div>
                                        @if ($lesson->type == 'document')
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#{{ $lesson->type }}-{{ $lesson->id }}">
                                                Xem
                                            </button>
                                            <div class="modal fade" id="{{ $lesson->type }}-{{ $lesson->id }}"
                                                tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close">
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <a href="{{ Storage::url($lesson->documents->document_url) }}"
                                                                download="{{ basename($lesson->documents->document_url) }}">
                                                                Tải xuống tài liệu
                                                            </a>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                data-bs-dismiss="modal" aria-label="Close">
                                                                Đóng
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($lesson->type == 'quiz')
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#{{ $lesson->type }}-{{ $lesson->id }}">
                                                Xem
                                            </button>
                                        @endif
                                        @if ($lesson->type == 'video')
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#{{ $lesson->type }}-{{ $lesson->id }}">
                                                Xem
                                            </button>
                                            <div class="modal fade gd-example-modal-lg" tabindex="-1" role="dialog"
                                                aria-labelledby="myLargeModalLabel"
                                                id="{{ $lesson->type }}-{{ $lesson->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close">
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <video controls class="w-100"
                                                                src="{{ Storage::url($lesson->videos->video_url) }}"></video>
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                data-bs-dismiss="modal" aria-label="Close">
                                                                Đóng
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($lesson->type == 'coding')
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#{{ $lesson->type }}-{{ $lesson->id }}">
                                                Xem
                                            </button>
                                        @endif

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
