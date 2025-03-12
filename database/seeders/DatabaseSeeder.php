<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Progress;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Review;
use App\Models\Role;
use App\Models\Section;
use App\Models\Tag;
use App\Models\Transaction;
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

        User::create([
            'name' => 'Học viên 2',
            'email' => 'hocvien2@gmail.com',
            'password' => Hash::make('123123123'),
            'phone_number' => '0333444555',

        ]);
        Wallet::create([
            'user_id' => 3,
            'balance' => 999000
        ]);
        $roleStudent = Role::select('id')->where('name', 'student')->first();
        UserRole::create([
            'user_id' => 3,
            'role_id' => 1,
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

        $lecturer = User::create([
            'name' => 'Tôn Nghộ Không',
            'email' => 'giangvienc@gmail.com',
            'password' => Hash::make('123123123'),
            'phone_number' => '0888777666',
            'profile_picture' => '',
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

        // $tags = [
        //     [
        //         'name' => 'php-laravel'
        //     ],
        //     [
        //         'name' => 'c#-.NET'
        //     ],
        //     [
        //         'name' => 'java-sp'
        //     ],
        //     [
        //         'name' => 'reactjs-js'
        //     ],
        // ];
        foreach ($categories as $category) {
            Category::create($category);
        }
        // foreach ($tags as $tag) {
        //     Tag::create($tag);
        // }

        $courseID = Course::create([
            'user_id' => 4,
            'category_id' => 3,
            'price_regular' => 199000,
            'price_sale' => 99000,
            'title' => 'Khoá học Laravel',
            'status' => 'published',
            'admin_commission_rate' => 30
        ]);
        // Course::create([
        //     'user_id' => 4,
        //     'category_id' => 1,
        //     'price_regular' => 500000,
        //     'price_sale' => 399000,
        //     'title' => 'Khoá học React',
        //     'status' => 'draft',
        //     'admin_commission_rate' => 30
        // ]);
        // Course::create([
        //     'user_id' => 4,
        //     'category_id' => 4,
        //     'price_regular' => 99000,
        //     'price_sale' => 89000,
        //     'title' => 'Khoá học PHP cơ bản',
        //     'status' => 'draft',
        //     'admin_commission_rate' => 30
        // ]);

        // Course::create([
        //     'user_id' => 5,
        //     'category_id' => 4,
        //     'title' => 'Khoá học PHP nâng cao',
        //     'status' => 'published',
        //     'is_free' => true,
        //     'admin_commission_rate' => 30
        // ]);

        // Course::create([
        //     'user_id' => 5,
        //     'category_id' => 3,
        //     'title' => 'Khoá học Laravel cơ bản',
        //     'status' => 'published',
        //     'is_free' => true,
        //     'admin_commission_rate' => 30
        // ]);

        // $quizzes = [
        //     [
        //         'lesson_id' => 1,
        //         'title' => 'cách cài đặt laragon?'
        //     ],
        //     [
        //         'lesson_id' => 2,
        //         'title' => 'cấu trúc thư mục của laragon như thế nào?'
        //     ]
        // ];
        // foreach ($quizzes as $quiz) {
        //     Quiz::create($quiz);
        // }
      
        // Course::create([
        //     'user_id' => 5,
        //     'category_id' => 5,
        //     'title' => 'Khoá học Js nâng cao',
        //     'status' => 'published',
        //     'is_free' => true,
        //     'admin_commission_rate' => 30
        // ]);

        // $sectionID = Section::create([
        //     'course_id' => $courseID->id,
        //     'title' => 'Cài đặt môi trường Laravel',
        //     'order' => 1
        // ]);

        // $lessonID = Lesson::create([
        //     'section_id' => $sectionID->id,
        //     'title' => 'Cài đặt laragon',
        //     'order' => 1,
        //     'type' => 'video'
        // ]);
        // Video::create([
        //     'lesson_id' => $lessonID->id,
        //     'video_url' => '',
        //     'duration' => 1248
        // ]);

        // Lesson::create([
        //     'section_id' => $sectionID->id,
        //     'title' => 'Cấu trúc thư mục laravel',
        //     'order' => 2,
        //     'type' => 'video'
        // ]);
        // Section::create([
        //     'course_id' => $courseID->id,
        //     'title' => 'Biến và Kiểu dữ liệu (Variables and Data types)',
        //     'order' => 2
        // ]);
        // $sectionID->update(['total_lessons' => $sectionID->lessons()->count()]);

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

        // // // Tạo lịch sử sử dụng voucher
        // VoucherUse::create([
        //     'voucher_id' => 1,
        //     'user_id' => $student->id,
        //     'course_id' => $courseID->id,
        //     'time_used' => Carbon::now(),
        // ]);

        // $enrollments = [
        //     [
        //         'user_id' => 2,
        //         'course_id' => 1,
        //         'status' => 'active',
        //         'enrolled_at' => Carbon::now('Asia/Ho_Chi_Minh')
        //     ],
        //     [
        //         'user_id' => 3,
        //         'course_id' => 1,
        //         'status' => 'active',
        //         'enrolled_at' => Carbon::now('Asia/Ho_Chi_Minh')
        //     ],
        // ];
        // foreach ($enrollments as $enrollment) {
        //     Enrollment::create($enrollment);
        // }

        // $quizzes = [
        //     [
        //         'title' => 'cách cài đặt laragon?'
        //     ],
        //     [
        //         'title' => 'cấu trúc thư mục của laragon như thế nào?'
        //     ]
        // ];
        // foreach ($quizzes as $quiz) {
        //     Quiz::create($quiz);
        // }

        // $questions = [
        //     [
        //         'quiz_id' => 1,
        //         'question_text' => 'Cách cài đặt Laragon?',
        //         'is_multiple_choice' => 0,
        //         'correct_answers' => json_encode(['Cài đặt bằng trình cài đặt chính thức']),
        //         'order' => 1
        //     ],
        //     [
        //         'quiz_id' => 2,
        //         'question_text' => 'Cấu trúc thư mục của Laragon như thế nào?',
        //         'is_multiple_choice' => 1,
        //         'correct_answers' => json_encode(['www', 'bin', 'etc', 'data']),
        //         'order' => 2
        //     ]
        // ];
        // foreach ($questions as $question) {
        //     Question::create($question);

    //         $reviews = [
    //             [
    //                 'user_id' => 4,
    //                 'course_id' => 3,
    //                 'rating' => 5,
    //                 'review_text' => 'Good job'
    //             ],
    //             [
    //                 'user_id' => 3,
    //                 'course_id' => 2,
    //                 'rating' => 2,
    //                 'review_text' => 'Very bad'
    //             ],
    //             [
    //                 'user_id' => 4,
    //                 'course_id' => 4,
    //                 'rating' => 4,
    //                 'review_text' => 'Very good'
    //             ],
    //             [
    //                 'user_id' => 4,
    //                 'course_id' => 5,
    //                 'rating' => 5,
    //                 'review_text' => 'Dinh noc kich tran'
    //             ]
    //         ];

    //         foreach ($reviews as $review) {
    //             Review::create($review);
    //         }
        

    //     $progressData = [
    //         [
    //             'user_id' => 2,
    //             'course_id' => 1,
    //             'status' => 'in_progress',
    //             'progress_percent' => 0
    //         ],
    //         [
    //             'user_id' => 3,
    //             'course_id' => 1,
    //             'status' => 'in_progress',
    //             'progress_percent' => 0
    //         ],
    //     ];
    //     foreach ($progressData as $data) {
    //         Progress::create($data);
    //     }

    //     $transactions = [
    //         [
    //             'user_id' => 2,
    //             'course_id' => 1,
    //             'amount' => 99000,
    //             'payment_method' => 'wallet',
    //             'status' => 'success',
    //             'transaction_date' => Carbon::now('Asia/Ho_Chi_Minh')
    //         ],
    //         [
    //             'user_id' => 3,
    //             'course_id' => 1,
    //             'amount' => 99000,
    //             'payment_method' => 'bank_transfer',
    //             'status' => 'success',
    //             'transaction_date' => Carbon::now('Asia/Ho_Chi_Minh')
    //         ],
    //     ];
    //     foreach ($transactions as $data) {
    //         Transaction::create($data);
    //     }


    //     Course::create([
    //         'user_id' => 4,
    //         'category_id' => 4,
    //         'price_regular' => 100000,
    //         'price_sale' => 70000,
    //         'title' => 'Khoá học PHP cơ bản',
    //         'status' => 'published',
    //         'admin_commission_rate' => 30
    //     ]);


    //     $comments = [
    //         [
    //             'user_id' => 1,
    //             'content' => 'Bài học rất hữu ích!',
    //             'parent_id' => null,
    //             'commentable_type' => 'App\Models\Lesson',
    //             'commentable_id' => 2,
    //         ],
    //         [
    //             'user_id' => 2,
    //             'content' => 'Cảm ơn giảng viên!',
    //             'parent_id' => null,
    //             'commentable_type' => Lesson::class,
    //             'commentable_id' => 1,
    //         ],
    //         [
    //             'user_id' => 3,
    //             'content' => 'Bạn có thể giải thích lại phần này không?',
    //             'parent_id' => 1, // Bình luận trả lời
    //             'commentable_type' => 'App\Models\Lesson',
    //             'commentable_id' => 2,
    //         ],
    //         [
    //             'user_id' => 4,
    //             'content' => 'Mình đã hiểu rồi, cảm ơn!',
    //             'parent_id' => 3, // Trả lời bình luận trên
    //             'commentable_type' => 'App\Models\Lesson',
    //             'commentable_id' => 2,
    //         ],
    //     ];

    //     foreach ($comments as $comment) {
    //         Comment::create($comment);
    //     }

        // Course::create([
        //     'user_id' => 4,
        //     'category_id' => 4,
        //     'price_regular' => 100000,
        //     'price_sale' => 70000,
        //     'title' => 'Khoá học PHP cơ bản',
        //     'status' => 'published',
        //     'admin_commission_rate' => 30
        // ]);

    }

}