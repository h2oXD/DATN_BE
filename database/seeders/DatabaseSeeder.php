<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Lesson;
use App\Models\Role;
use App\Models\Section;
use App\Models\Student;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Video;
use App\Models\Voucher;
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
            'profile_picture' => '/profile_pictures/EtUIcPlzMJiTg9JiTQl1Lm6XSY3RRTJ0mmZHC1Xx.jpg',
        ]);
        Student::create([
            'user_id' => $student->id,
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
            'profile_picture' => '/profile_pictures/RkukY0gX1gZ7vlcCNkqVTaA5SejQFlAVm1BGStq3.jpg',
        ]);
        Student::create([
            'user_id' => $lecturer->id,
        ]);
        $lecturerID = Lecturer::create([
            'user_id' => $lecturer->id,
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
            'lecturer_id' => 1,
            'category_id' => 3,
            'title' => 'Khoá học Laravel',
            'status' => 'pending',
            'admin_commission_rate' => 30
        ]);
        Course::create([
            'lecturer_id' => 1,
            'category_id' => 1,
            'title' => 'Khoá học React',
            'status' => 'pending',
            'admin_commission_rate' => 30
        ]);
        Course::create([
            'lecturer_id' => 1,
            'category_id' => 4,
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
            'course_id' => $courseID->id,
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
            'course_id' => $courseID->id,
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
                'name' => 'Giảm phí vận chuyển',
                'code' => 'DVC10',
                'description' => 'Giảm giá phí vận chuyển 10k VND',
                'type' => 'fix_amount',
                'discount_percent' => null,
                'discount_amount' => '10000',
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
                'discount_percent' => '10',
                'discount_amount' => null,
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
                'discount_percent' => '100',
                'discount_amount' => null,
                'start_time' => Carbon::now(),
                'end_time' => Carbon::now()->addDays(30),
                'count' => '9999',
                'is_active' => '0',
            ],
        ];
        foreach ($vouchers as $voucher) {
            Voucher::create($voucher);
        }
    }
}
