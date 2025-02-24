<html>

<head>
    <style type='text/css'>
        body, html {
            margin: 0;
            padding: 0;
        }

        body {
            color: black;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Georgia, serif;
            text-align: center;
        }

        .container {
            border: 15px solid tan;
            width: 800px;
            padding: 40px;
            text-align: center;
        }

        .logo {
            color: tan;
            font-size: 20px;
            font-weight: bold;
        }

        .title {
            color: tan;
            font-size: 42px;
            font-weight: bold;
            margin: 20px 0;
        }

        .assignment {
            font-size: 20px;
            margin: 20px;
        }

        .person {
            border-bottom: 2px solid black;
            font-size: 30px;
            font-style: italic;
            font-weight: bold;
            margin: 20px auto;
            width: 60%;
        }

        .reason {
            font-size: 18px;
            margin: 20px;
        }

        .date {
            margin-top: 30px;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            {{ config('app.name') }} <!-- Tên tổ chức lấy từ Laravel -->
        </div>

        <div class="title">
            Certificate of Completion
        </div>

        <div class="assignment">
            This certificate is presented to
        </div>

        <div class="person">
            {{ $user->name }}
        </div>

        <div class="reason">
            For successfully completing the course<br />
            <strong>{{ $course->title }}</strong>
        </div>

        <div class="date">
            Issued on: {{ now()->format('F d, Y') }}
        </div>
    </div>
</body>

</html>
