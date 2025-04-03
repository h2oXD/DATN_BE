<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo phê duyệt khóa học</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f2f4f8;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
            font-family: "Inter", sans-serif;
            line-height: 1.7;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        .email-container {
            width: 100%;
            max-width: 700px;
            margin: 60px auto;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            position: relative;
        }

        .email-header {
            background: linear-gradient(135deg, #754ffe 0%, #4a31c1 70%, #3b2a9f 100%);
            color: #ffffff;
            padding: 50px 30px 70px;
            text-align: center;
            position: relative;
            clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
        }

        .email-header::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0) 70%);
            pointer-events: none;
        }

        .email-header h1 {
            font-size: 32px;
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.5px;
            position: relative;
            z-index: 1;
        }

        .email-body {
            padding: 40px 50px;
            background: #fff;
            border-radius: 0 0 20px 20px;
            position: relative;
            z-index: 2;
        }

        .email-body h2 {
            color: #754ffe;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
        }

        .email-body h2::before {
            content: "🎉";
            margin-right: 10px;
            font-size: 20px;
        }

        .email-body p {
            font-size: 16px;
            margin-bottom: 20px;
            color: #333;
            font-weight: 400;
        }

        .email-body strong {
            font-weight: 600;
            color: #1a1a1a;
        }

        .success-box {
            background: linear-gradient(145deg, #e6fff0 0%, #eef2ff 100%);
            padding: 20px 25px;
            border-radius: 12px;
            border-left: 6px solid #754ffe;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .success-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .cta-button {
            display: inline-block;
            padding: 14px 30px;
            background: linear-gradient(90deg, #754ffe 0%, #5e3fd8 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            box-shadow: 0 4px 15px rgba(117, 79, 254, 0.4);
            transition: all 0.3s ease;
        }

        .cta-button:hover {
            background: linear-gradient(90deg, #5e3fd8 0%, #754ffe 100%);
            box-shadow: 0 6px 20px rgba(117, 79, 254, 0.6);
            transform: translateY(-2px);
        }

        .email-footer {
            background: linear-gradient(135deg, #2a1e5c 0%, #3b2a9f 100%);
            color: #d9e0ff;
            text-align: center;
            padding: 25px;
            font-size: 14px;
            font-weight: 300;
            border-radius: 0 0 20px 20px;
        }

        .email-footer a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 400;
            padding: 0 5px;
            transition: color 0.3s ease;
        }

        .email-footer a:hover {
            color: #754ffe;
        }

        .divider {
            width: 50px;
            height: 2px;
            background: #754ffe;
            margin: 20px auto;
            border-radius: 2px;
        }

        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 30px 10px;
                border-radius: 16px;
            }

            .email-header {
                padding: 30px 20px 50px;
            }

            .email-body {
                padding: 30px;
            }

            .email-header h1 {
                font-size: 26px;
            }

            .email-body h2 {
                font-size: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Chúc mừng! Khóa học đã được phê duyệt</h1>
        </div>
        <div class="email-body">
            <h2>Xin chào {{ $notifiable->name }}</h2>
            <p>Chúng tôi rất vui mừng thông báo rằng khóa học "<strong>{{ $course->title }}</strong>" của bạn đã được
                phê duyệt thành công!</p>
            <div class="success-box">
                <p>Khóa học của bạn hiện đã sẵn sàng để xuất hiện trên nền tảng {{ config('app.name') }} và chào đón học
                    viên.</p>
            </div>
            <p>Bạn có thể bắt đầu quảng bá khóa học ngay bây giờ hoặc kiểm tra lại nội dung để đảm bảo mọi thứ hoàn hảo.
                Nếu cần hỗ trợ thêm, đội ngũ chúng tôi luôn ở đây để giúp bạn!</p>
            {{-- <p><a href="{{ url('/course/' . $course->id) }}" class="cta-button">Xem khóa học ngay</a></p> --}}
            <p>Chúc bạn thành công với hành trình giảng dạy!</p>
            <p><strong>Đội ngũ {{ config('app.name') }}</strong></p>
        </div>
        <div class="email-footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. Mọi quyền được bảo lưu.</p>
            <div class="divider"></div>
            <p>
                <a href="{{ url('/contact') }}">Liên hệ</a> |
                <a href="{{ url('/') }}">Website</a> |
                <a href="{{ url('/support') }}">Hỗ trợ</a>
            </p>
        </div>
    </div>
</body>

</html>
