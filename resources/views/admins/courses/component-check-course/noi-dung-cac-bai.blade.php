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
                                            <svg stroke="currentColor" fill="currentColor" stroke-width="0"
                                                viewBox="0 0 24 24" class="tw-size-5 me-2" height="1.2em" width="1.2em"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill="none" d="M0 0h24v24H0V0z"></path>
                                                <path
                                                    d="M9 7v8l7-4zm12-4H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h5v2h8v-2h5c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 14H3V5h18v12z">
                                                </path>
                                            </svg>
                                        @endif
                                        @if ($lesson->type == 'coding')
                                            <i class="fe fe-code"></i>
                                        @endif
                                        {{ $lesson->title }}
                                        @if ($lesson->type == 'video')
                                            @if ($lesson->videos->duration < 5)
                                                <span class="text-warning">[{{ $lesson->videos->duration }} phút] <i class="fe fe-alert-triangle"></i></span>
                                            @else
                                                <span class="text-success">[{{ $lesson->videos->duration }} phút] <i class="fe fe-check"></i></span>
                                            @endif
                                        @endif
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
