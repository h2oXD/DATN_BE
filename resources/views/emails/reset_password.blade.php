<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        p {
            font-size: 16px;
            color: #555;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            color: #ffffff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            

        }
        .btn:hover {
            background-color: #0056b3;
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #ffffff;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Xin chào, {{ $email }}</h1>
        <p>Bạn đã yêu cầu đặt lại mật khẩu.</p>
        <p>Nhấn vào nút dưới đây để đặt lại mật khẩu:</p>
        <a href="{{ $resetUrl }}" class="btn">Đặt lại mật khẩu</a>
        <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
        <p class="footer">Cảm ơn,<br>{{ config('app.name') }}</p>
    </div>
</body>
</html>
