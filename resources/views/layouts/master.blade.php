<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="author" content="LoraSpace" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            <div class="">
                @yield('content')
            </div>

        </main>
    </div>
    <script>
        document.documentElement.setAttribute("data-bs-theme", "light");
    </script>
    @yield('script')
    <script>
        document.documentElement.setAttribute("data-bs-theme", "light");
    </script>
    @stack('scripts')
    @include('layouts.partials.script')
    @vite('resources/js/public.js')
    <script>
        var userId = {{ Auth::user()->id }}
    </script>
</body>


</html>
