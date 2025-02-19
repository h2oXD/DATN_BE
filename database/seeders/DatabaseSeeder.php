<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Role;
use App\Models\Section;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Video;
use App\Models\Voucher;
use App\Models\Wallet;
use App\Models\VoucherUse;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $roles = ['student', 'lecturer', 'admin'];
        foreach ($roles as $role) {
            Role::create([
                'name' => $role,
            ]);
        }

        //Tạo admin
        $admin = User::create([
            'name' => 'Nguyễn Hữu Hào',
            'email' => 'haonhph45336@fpt.edu.vn',
            'password' => Hash::make('123123123'),
            'profile_picture' => '/profile_pictures/yprFYFlo7R4PR133ors5ETZYFCrxmPIaZLZv2sMz.jpg',
        ]);
        $roleAdmin = Role::select('id')->where('name', 'admin')->first();
        UserRole::create([
            'user_id' => $admin->id,
            'role_id' => $roleAdmin->id,
        ]);

        //Tạo Học viên
        $student = User::create([
            'name' => 'Học viên 1',
            'email' => 'hocvien1@gmail.com',
            'password' => Hash::make('123123123'),
            'phone_number' => '0333444555',
            'profile_picture' => '/profile_pictures/EtUIcPlzMJiTg9JiTQl1Lm6XSY3RRTJ0mmZHC1Xx.jpg',
        ]);
        Wallet::create([
            'user_id' => $student->id,
            'balance' => 999000
        ]);
        $roleStudent = Role::select('id')->where('name', 'student')->first();
        UserRole::create([
            'user_id' => $student->id,
            'role_id' => $roleStudent->id,
        ]);

        //Tạo giảng viên
        $lecturer = User::create([
            'name' => 'Giảng viên A',
            'email' => 'giangviena@gmail.com',
            'password' => Hash::make('123123123'),
            'phone_number' => '0888777666',
            'profile_picture' => '/profile_pictures/RkukY0gX1gZ7vlcCNkqVTaA5SejQFlAVm1BGStq3.jpg',
        ]);
        Wallet::create([
            'user_id' => $lecturer->id,
            'balance' => 0
        ]);
        $roleLecturer = Role::select('id')->where('name', 'lecturer')->first();
        UserRole::create([
            'user_id' => $lecturer->id,
            'role_id' => $roleStudent->id,
        ]);
        UserRole::create([
            'user_id' => $lecturer->id,
            'role_id' => $roleLecturer->id,
        ]);

        $lecturer = User::create([
            'name' => 'Giảng viên B',
            'email' => 'giangvienb@gmail.com',
            'password' => Hash::make('123123123'),
            'phone_number' => '0888777666',
            'profile_picture' => '/profile_pictures/RkukY0gX1gZ7vlcCNkqVTaA5SejQFlAVm1BGStq3.jpg',
        ]);

        $lecturer->wallet()->create(['balance' => 0]);

        $roles = Role::whereIn('name', ['student', 'lecturer'])->pluck('id');
        $lecturer->roles()->attach($roles);

        $categories = [
            [
                'id' => 1,
                'name' => 'Công nghệ thông tin',
            ],
            [
                'id' => 2,
                'name' => 'Thiết kế đồ hoạ',
            ],
            [
                'name' => 'Laravel',
                'parent_id' => 1
            ],
            [
                'name' => 'PHP',
                'parent_id' => 1
            ],
            [
                'name' => 'JavaScript',
                'parent_id' => 1
            ],
            [
                'name' => 'Photoshop',
                'parent_id' => 2
            ],
            [
                'name' => 'Premiere',
                'parent_id' => 2
            ],
            [
                'name' => 'After Effect',
                'parent_id' => 2
            ],
        ];

        $tags = [
            [
                'name' => 'php-laravel'
            ],
            [
                'name' => 'c#-.NET'
            ],
            [
                'name' => 'java-sp'
            ],
            [
                'name' => 'reactjs-js'
            ],
        ];
        foreach ($categories as $category) {
            Category::create($category);
        }
        foreach ($tags as $tag) {
            Tag::create($tag);
        }

        $courseID = Course::create([
            'user_id' => 3,
            'category_id' => 3,
            'price_regular' => 199000,
            'price_sale' => 99000,
            'title' => 'Khoá học Laravel',
            'status' => 'pending',
            'admin_commission_rate' => 30
        ]);
        Course::create([
            'user_id' => 3,
            'category_id' => 1,
            'price_regular' => 500000,
            'price_sale' => 399000,
            'title' => 'Khoá học React',
            'status' => 'pending',
            'admin_commission_rate' => 30
        ]);
        Course::create([
            'user_id' => 3,
            'category_id' => 4,
            'price_regular' => 99000,
            'price_sale' => 89000,
            'title' => 'Khoá học PHP cơ bản',
            'status' => 'pending',
            'admin_commission_rate' => 30
        ]);

        $sectionID = Section::create([
            'course_id' => $courseID->id,
            'title' => 'Cài đặt môi trường Laravel',
            'order' => 1
        ]);
        $lessonID = Lesson::create([
            'section_id' => $sectionID->id,
            'title' => 'Cài đặt laragon',
            'order' => 1
        ]);
        Video::create([
            'lesson_id' => $lessonID->id,
            'video_url' => 'https://www.youtube.com/watch?v=IZfc3xFhQ2k',
            'duration' => 1248
        ]);
        Lesson::create([
            'section_id' => $sectionID->id,
            'title' => 'Cấu trúc thư mục laravel',
            'order' => 2
        ]);
        Section::create([
            'course_id' => $courseID->id,
            'title' => 'Biến và Kiểu dữ liệu (Variables and Data types)',
            'order' => 2
        ]);

        // Tạo phiếu giảm giá
        $vouchers = [
            [
                'name' => 'Giảm giá thanh toán',
                'code' => 'DVC10',
                'description' => 'Giảm giá thanh toán 10k VND',
                'type' => 'fix_amount',
                'discount_price' => 10000,
                'start_time' => Carbon::now(),
                'end_time' => Carbon::now()->addDays(30),
                'count' => '100',
                'is_active' => '1',
            ],
            [
                'name' => 'Giảm giá sản phẩm',
                'code' => 'DSP10',
                'description' => 'Giảm giá sản phẩm 10%',
                'type' => 'percent',
                'discount_price' => 10,
                'start_time' => Carbon::now(),
                'end_time' => Carbon::now()->addDays(30),
                'count' => '99',
                'is_active' => '1',
            ],
            [
                'name' => 'Tặng khóa học đầu tiên',
                'code' => 'FREE100',
                'description' => 'Miễn phí thanh toán khi tham gia khóa học đầu tiên',
                'type' => 'percent',
                'discount_price' => 10,
                'start_time' => Carbon::now(),
                'end_time' => Carbon::now()->addDays(30),
                'count' => '9999',
                'is_active' => '0',
            ],
        ];
        foreach ($vouchers as $voucher) {
            Voucher::create($voucher);
        }

        // Tạo lịch sử sử dụng voucher
        VoucherUse::create([
            'voucher_id'    => 1,
            'user_id'       => $student->id,
            'course_id'     => $courseID->id,
            'time_used'     => Carbon::now(),
        ]);
    }
}
