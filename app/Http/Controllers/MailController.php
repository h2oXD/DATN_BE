<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

class MailController extends Controller
{
    public function sendEmail()
    {
        $details = [
            'title' => 'Chào mừng đến với Loraspace',
            'body' => 'Đây là email gửi từ ứng dụng Loraspace.'
        ];

        Mail::to('loraspace8386@gmail.com')->send(new SendMail($details));


        return 'Email đã được gửi thành công!';
    }
}
