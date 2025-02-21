@extends('layouts.master')

@section('title')
    Kiểm duyệt khoá học - {{ $course->title }}
@endsection

@section('content')
    <div class="m-3">
        @include('admins.courses.component-check-course.thanh-kiem-duyet')
        @include('admins.courses.component-check-course.tab')
        <div class="card mt-2" id="tabContent">
            <div class="tab-content">
                <!-- Tab All -->
                @include('admins.courses.component-check-course.noi-dung-cac-bai')

                @include('admins.courses.component-check-course.tong-quan')

                @include('admins.courses.component-check-course.thong-tin')

                @include('admins.courses.component-check-course.kiem-tra')
            </div>
        </div>
    </div>
@endsection
