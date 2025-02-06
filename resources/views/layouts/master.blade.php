<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="author" content="LoraSpace" />
    <title>@yield('title')</title>
    @yield('style')
    @include('layouts.partials.style')
</head>

<body>
    <!-- Wrapper -->
    <div id="db-wrapper">
        <!-- navbar vertical -->
        <!-- Sidebar -->
        @include('layouts.components.navbar')

        <!-- Page Content -->
        <main id="page-content">
            @include('layouts.components.header')

            <!-- Page Header -->
            <!-- Container fluid -->
            <div class="container my-5">
                @yield('content')
            </div>

        </main>
    </div>
    @yield('script')
    @include('layouts.partials.script')
</body>


</html>
