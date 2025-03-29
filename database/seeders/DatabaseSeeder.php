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
use Illuminate\Support\Str;


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
            'profile_picture' => '',
        ]);
        $roleAdmin = Role::select('id')->where('name', 'admin')->first();
        UserRole::create([
            'user_id' => $admin->id,
            'role_id' => $roleAdmin->id,
        ]);

        //Tạo Học viên
        $student = User::create([
            'name' => 'Trương Thái Tú',
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
            'name' => 'Nguyễn Văn Thuyết',
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
            'name' => 'Tống Văn Đức',
            'email' => 'giangviena@gmail.com',
            'password' => Hash::make('123123123'),
            'phone_number' => '0888777666',
            'profile_picture' => '',
            'bio' => 'Là một giảng viên lập trình với hơn 10 năm kinh nghiệm trong ngành công nghệ thông tin, tôi luôn đam mê chia sẻ kiến thức và truyền cảm hứng cho thế hệ lập trình viên tương lai. Tôi tin rằng lập trình không chỉ là một kỹ năng, mà còn là một nghệ thuật, một cách tư duy sáng tạo để giải quyết vấn đề.

Trong suốt sự nghiệp của mình, tôi đã có cơ hội làm việc với nhiều ngôn ngữ lập trình khác nhau, từ những ngôn ngữ cổ điển như C++ đến những ngôn ngữ hiện đại như Python và JavaScript. Tôi cũng có kinh nghiệm sâu rộng trong việc phát triển các ứng dụng web, ứng dụng di động và các hệ thống phần mềm phức tạp.

Ngoài công việc giảng dạy, tôi cũng thường xuyên tham gia vào các dự án mã nguồn mở và các hoạt động cộng đồng liên quan đến lập trình. Tôi luôn mong muốn được đóng góp vào sự phát triển của ngành công nghệ thông tin và giúp đỡ những người có đam mê với lập trình.'
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
            'name' => 'Nguyễn Văn Thuyết',
            'email' => 'giangvienb@gmail.com',
            'password' => Hash::make('123123123'),
            'phone_number' => '0888777666',
            'profile_picture' => '',
            'bio' => 'Với hơn 8 năm kinh nghiệm trong lĩnh vực phát triển phần mềm, tôi đã chuyển niềm đam mê của mình sang giảng dạy để giúp những lập trình viên tham vọng khám phá tiềm năng của họ. Tôi tin rằng học lập trình không chỉ là việc nắm vững cú pháp, mà còn là việc rèn luyện tư duy logic và khả năng giải quyết vấn đề.

Tôi chuyên về các ngôn ngữ lập trình web như JavaScript, React và Node.js. Tôi luôn cập nhật những xu hướng công nghệ mới nhất để đảm bảo rằng học viên của mình được trang bị những kiến thức và kỹ năng cần thiết để thành công trong ngành.

Ngoài việc giảng dạy, tôi cũng là một người đóng góp tích cực cho cộng đồng mã nguồn mở. Tôi tin rằng việc chia sẻ kiến thức và kinh nghiệm là chìa khóa để xây dựng một cộng đồng lập trình viên mạnh mẽ và đoàn kết.'
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
            'bio' => 'Với hơn 15 năm kinh nghiệm trong lĩnh vực phát triển phần mềm và nghiên cứu khoa học máy tính, tôi mang đến lớp học không chỉ kiến thức lập trình mà còn là tư duy của một nhà khoa học. Tôi tin rằng lập trình là công cụ mạnh mẽ để hiện thực hóa những ý tưởng sáng tạo và giải quyết những vấn đề phức tạp.

Tôi có chuyên môn sâu về các lĩnh vực như trí tuệ nhân tạo, học máy và phân tích dữ liệu lớn. Tôi luôn khuyến khích học viên của mình đặt câu hỏi, thử nghiệm và không ngừng khám phá những giới hạn của công nghệ.

Ngoài công việc giảng dạy, tôi còn tham gia vào các dự án nghiên cứu và phát triển các sản phẩm công nghệ đột phá. Tôi mong muốn được chia sẻ những kinh nghiệm và kiến thức của mình để giúp học viên trở thành những lập trình viên xuất sắc và những nhà lãnh đạo công nghệ tương lai.',
        ]);

        $lecturer->wallet()->create(['balance' => 0]);

        $roles = Role::whereIn('name', ['student', 'lecturer'])->pluck('id');
        $lecturer->roles()->attach($roles);

        $listLecturers = [
            [
                'name' => 'Nguyễn Ngọc Hiếu',
                'email' => 'giangvien10@gmail.com',
                'password' => '123123123',
                'phone_number' => '0888777666',
                'profile_picture' => '',
                'bio' => ''
            ],
            [
                'name' => 'Trông Anh Ngược',
                'email' => 'giangvien11@gmail.com',
                'password' => '123123123',
                'phone_number' => '0888777666',
                'profile_picture' => '',
                'bio' => ''
            ],
            [
                'name' => 'Giả Hành Tôn',
                'email' => 'giangvien12@gmail.com',
                'password' => '123123123',
                'phone_number' => '0888777666',
                'profile_picture' => '',
                'bio' => ''
            ],
            [
                'name' => 'Tôn Hành Giả',
                'email' => 'giangvien13@gmail.com',
                'password' => '123123123',
                'phone_number' => '0888777666',
                'profile_picture' => '',
                'bio' => ''
            ],
        ];
        foreach ($listLecturers as $l) {
            $lecturer = User::create([
                'name' => $l['name'],
                'email' => $l['email'],
                'password' => Hash::make($l['password']),
                'phone_number' => $l['phone_number'],
                'profile_picture' => '',
                'bio' => '',
            ]);

            $lecturer->wallet()->create(['balance' => 0]);

            $roles = Role::whereIn('name', ['student', 'lecturer'])->pluck('id');
            $lecturer->roles()->attach($roles);
        }

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

        // $courseID = Course::create([
        //     'user_id' => 4,
        //     'category_id' => 3,
        //     'price_regular' => 199000,
        //     'price_sale' => 99000,
        //     'title' => 'Khoá học Laravel',
        //     'status' => 'published',
        //     'admin_commission_rate' => 30
        // ]);
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


        // $chatRooms = [
        //     [
        //         'id' => 1,
        //         'course_id' => Str::uuid()->toString(),
        //         'owner_id' => 1,
        //         'name' => 'General Discussion',
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //     ],
        //     [
        //         'id' => 2,
        //         'course_id' => Str::uuid()->toString(),
        //         'owner_id' => 2,
        //         'name' => 'Project Collaboration',
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //     ],
        //     [
        //         'id' => 3,
        //         'course_id' => Str::uuid()->toString(),
        //         'owner_id' => 3,
        //         'name' => 'Q&A Session',
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //     ],
        // ];



    }

}