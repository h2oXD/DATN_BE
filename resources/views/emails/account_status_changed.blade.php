<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo thay đổi trạng thái tài khoản</title>
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
            content: "📢";
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
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Thông báo thay đổi trạng thái tài khoản</h1>
        </div>
        <div class="email-body">
            <h2>Kính gửi {{ $user->name }}</h2>
            <p>Chúng tôi xin thông báo rằng trạng thái tài khoản của bạn đã được cập nhật. Trạng thái hiện tại của tài
                khoản là:</p>
            <p><strong>
                    @switch($user->status)
                        @case(0)
                            Đang hoạt động
                        @break

                        @case(1)
                            Đã khóa chức năng giảng viên
                        @break

                        @case(2)
                            Đã khóa chức năng giảng viên và học viên
                        @break
                    @endswitch
                </strong></p>
            <p>Trong trường hợp bạn có bất kỳ câu hỏi hay thắc mắc nào, xin vui lòng liên hệ với đội ngũ quản trị viên
                của chúng tôi để được hỗ trợ.</p>
            <p>Trân trọng,<br>Đội ngũ quản trị hệ thống</p>
        </div>
        <div class="email-footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. Tất cả quyền lợi được bảo lưu.</p>
            <p>
                <a href="{{ url('/contact') }}">Liên hệ với chúng tôi</a> |
                <a href="{{ url('/') }}">Trang chủ</a> |
                <a href="{{ url('/support') }}">Trung tâm hỗ trợ</a>
            </p>
        </div>
    </div>
</body>

</html>
