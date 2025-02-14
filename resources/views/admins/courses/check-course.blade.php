@extends('layouts.master')
@section('title')
    Kiểm duyệt khoá học - {{ $course->title }}
@endsection

@section('content')
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
@endsection
