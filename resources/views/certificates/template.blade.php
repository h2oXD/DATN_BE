<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate</title>
    <link rel="stylesheet" />
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
            src: url("{{ storage_path('fonts/DejaVuSans.ttf') }}") format('truetype');
        }

        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
        }

        .border-pattern {
            position: absolute;
            left: 4mm;
            top: -6mm;
            height: 200mm;
            width: 267mm;
            border: 1mm solid #991B1B;
            /* http://www.heropatterns.com/ */
            background-color: #d6d6e4;
            background-image: url("{{ asset('pattern.svg') }}");
        }

        .content {
            position: absolute;
            left: 10mm;
            top: 10mm;
            height: 178mm;
            width: 245mm;
            border: 1mm solid #991B1B;
            background: white;
        }

        .inner-content {
            border: 1mm solid #991B1B;
            margin: 4mm;
            padding: 10mm;
            height: 148mm;
            text-align: center;
        }

        h1 {
            text-transform: uppercase;
            font-size: 48pt;
            margin-bottom: 0;
            margin-top: 0mm;
        }

        h2 {
            font-size: 24pt;
            margin-top: 0;
            padding-bottom: 1mm;
            display: inline-block;
            border-bottom: 1mm solid #991B1B;
        }

        h2::after {
            content: "";
            display: block;
            padding-bottom: 4mm;
            border-bottom: 1mm solid #991B1B;
        }

        h3 {
            font-size: 25pt;
            margin-bottom: 0;
            margin-top: 0mm;
        }

        p {
            font-size: 16pt;
            text-decoration: bold;
        }

        .badge {
            width: 40mm;
            height: 40mm;
            position: absolute;
            right: 10mm;
            bottom: 10mm;
            background-image: url("{{ asset('badge.svg') }}");
        }
    </style>
</head>

<body>
    <div class="border-pattern">
        <div class="content">
            <div class="inner-content">
                <h1>Certificate</h1>
                <h2> <strong><i>{{ config('app.name') }}</i></strong></h2>
                <h3>This Certificate Is Proudly Presented To</h3>
                <p><strong><i>{{ $user->name }}</i></strong></p>
                <h3>Has Completed</h3>
                <p><strong><i>{{ $course->title }}</i></strong></p>
                <h3>On</h3>
                <p><strong><i>{{ now()->format('F d, Y') }}</i></strong></p>
                <div class="badge">
                </div>
            </div>
        </div>
    </div>
</body>

</html>
