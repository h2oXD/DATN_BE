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
            'status' => 'draft',
            'admin_commission_rate' => 30
        ]);
        Course::create([
            'lecturer_id' => 1,
            'category_id' => 1,
            'title' => 'Khoá học React',
            'status' => 'draft',
            'admin_commission_rate' => 30
        ]);
        Course::create([
            'lecturer_id' => 1,
            'category_id' => 4,
            'title' => 'Khoá học PHP cơ bản',
            'status' => 'draft',
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
    }
}
