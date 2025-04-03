<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Course;
use App\Models\CourseApprovalHistory;
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
            'name' => 'Vũ Đức Tài',
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
            'email' => 'fixbugandcry@gmail.com',
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
            // Lĩnh vực Công nghệ thông tin
            ['id' => 1, 'name' => 'Công nghệ thông tin'],
            ['id' => 2, 'name' => 'Thiết kế đồ hoạ'],
            ['id' => 3, 'name' => 'Kinh doanh & Marketing'],
            ['id' => 4, 'name' => 'Phát triển cá nhân'],
            ['id' => 5, 'name' => 'Ngôn ngữ & Dịch thuật'],

            // Công nghệ thông tin - Các lĩnh vực con
            ['name' => 'Laravel', 'parent_id' => 1],
            ['name' => 'PHP', 'parent_id' => 1],
            ['name' => 'JavaScript', 'parent_id' => 1],
            ['name' => 'Node.js', 'parent_id' => 1],
            ['name' => 'ReactJS', 'parent_id' => 1],
            ['name' => 'VueJS', 'parent_id' => 1],
            ['name' => 'Python', 'parent_id' => 1],
            ['name' => 'Machine Learning', 'parent_id' => 1],
            ['name' => 'Data Science', 'parent_id' => 1],
            ['name' => 'Cyber Security', 'parent_id' => 1],
            ['name' => 'DevOps', 'parent_id' => 1],
            ['name' => 'Database', 'parent_id' => 1],
            ['name' => 'SQL', 'parent_id' => 1],
            ['name' => 'NoSQL', 'parent_id' => 1],
            ['name' => 'Mobile Development', 'parent_id' => 1],
            ['name' => 'Android', 'parent_id' => 1],
            ['name' => 'iOS', 'parent_id' => 1],
            ['name' => 'Game Development', 'parent_id' => 1],

            // Thiết kế đồ hoạ - Các lĩnh vực con
            ['name' => 'Photoshop', 'parent_id' => 2],
            ['name' => 'Premiere', 'parent_id' => 2],
            ['name' => 'After Effect', 'parent_id' => 2],
            ['name' => 'Illustrator', 'parent_id' => 2],
            ['name' => 'UI/UX Design', 'parent_id' => 2],
            ['name' => '3D Modeling', 'parent_id' => 2],
            ['name' => 'Blender', 'parent_id' => 2],
            ['name' => 'Maya', 'parent_id' => 2],
            ['name' => 'Animation', 'parent_id' => 2],
            ['name' => 'Motion Graphics', 'parent_id' => 2],

            // Kinh doanh & Marketing - Các lĩnh vực con
            ['name' => 'Digital Marketing', 'parent_id' => 3],
            ['name' => 'SEO', 'parent_id' => 3],
            ['name' => 'Quảng cáo Facebook', 'parent_id' => 3],
            ['name' => 'Quảng cáo Google', 'parent_id' => 3],
            ['name' => 'Content Marketing', 'parent_id' => 3],
            ['name' => 'Bán hàng & Thương mại điện tử', 'parent_id' => 3],
            ['name' => 'Quản lý thương hiệu', 'parent_id' => 3],

            // Phát triển cá nhân - Các lĩnh vực con
            ['name' => 'Kỹ năng giao tiếp', 'parent_id' => 4],
            ['name' => 'Tư duy phản biện', 'parent_id' => 4],
            ['name' => 'Lãnh đạo & Quản lý', 'parent_id' => 4],
            ['name' => 'Kỹ năng thuyết trình', 'parent_id' => 4],
            ['name' => 'Kỹ năng làm việc nhóm', 'parent_id' => 4],
            ['name' => 'Quản lý thời gian', 'parent_id' => 4],

            // Ngôn ngữ & Dịch thuật - Các lĩnh vực con
            ['name' => 'Tiếng Anh', 'parent_id' => 5],
            ['name' => 'Tiếng Trung', 'parent_id' => 5],
            ['name' => 'Tiếng Nhật', 'parent_id' => 5],
            ['name' => 'Tiếng Hàn', 'parent_id' => 5],
            ['name' => 'Phiên dịch & Biên dịch', 'parent_id' => 5],
            ['name' => 'Luyện thi IELTS', 'parent_id' => 5],
            ['name' => 'Luyện thi TOEIC', 'parent_id' => 5],
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
                'discount_max_price' => 20000,
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
                'discount_price' => 100,
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

        // $courses = [
        //     [
        //         'id' => Str::uuid(),
        //         'user_id' => 2,
        //         'category_id' => 3,
        //         'price_regular' => 800000,
        //         'price_sale' => 600000,
        //         'title' => 'Phát triển ứng dụng di động với Flutter',
        //         'thumbnail' => 'courses/flutter.jpg',
        //         'video_preview' => 'courses/flutter-preview.mp4',
        //         'description' => 'Học cách tạo ứng dụng di động với Flutter.',
        //         'primary_content' => 'Flutter, Dart, Mobile App',
        //         'status' => 'draft',
        //         'is_show_home' => 0,
        //         'target_students' => json_encode(['Lập trình viên mobile', 'Người muốn học Flutter']),
        //         'learning_outcomes' => json_encode(['Phát triển ứng dụng đa nền tảng', 'Sử dụng Flutter hiệu quả']),
        //         'prerequisites' => json_encode(['Có kiến thức cơ bản về lập trình']),
        //         'who_is_this_for' => 'Dành cho lập trình viên muốn học Flutter.',
        //         'is_free' => 0,
        //         'language' => 'Tiếng Việt',
        //         'level' => 'Nâng cao',
        //         'admin_commission_rate' => 25.00,
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //         'submited_at' => null,
        //         'censored_at' => null,
        //         'admin_comment' => null,
        //     ],
        //     [
        //         'id' => Str::uuid(),
        //         'user_id' => 2,
        //         'category_id' => 5,
        //         'price_regular' => 400000,
        //         'price_sale' => 200000,
        //         'title' => 'Nhập môn lập trình Python',
        //         'thumbnail' => 'courses/python.jpg',
        //         'video_preview' => 'courses/python-preview.mp4',
        //         'description' => 'Khóa học giúp bạn làm quen với Python.',
        //         'primary_content' => 'Python, Cơ bản, Lập trình',
        //         'status' => 'published',
        //         'is_show_home' => 1,
        //         'target_students' => json_encode(['Người chưa biết lập trình', 'Sinh viên IT']),
        //         'learning_outcomes' => json_encode(['Nắm vững kiến thức Python', 'Xây dựng chương trình đơn giản']),
        //         'prerequisites' => json_encode(['Không yêu cầu kinh nghiệm']),
        //         'who_is_this_for' => 'Dành cho người mới học lập trình.',
        //         'is_free' => 1,
        //         'language' => 'Tiếng Việt',
        //         'level' => 'Cơ bản',
        //         'admin_commission_rate' => 20.00,
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //         'submited_at' => Carbon::now(),
        //         'censored_at' => Carbon::now(),
        //         'admin_comment' => 'Khóa học miễn phí chất lượng.',
        //     ],
        //     [
        //         'id' => Str::uuid(),
        //         'user_id' => 2,
        //         'category_id' => 2,
        //         'price_regular' => 900000,
        //         'price_sale' => 700000,
        //         'title' => 'Machine Learning với Python',
        //         'thumbnail' => 'courses/machine-learning.jpg',
        //         'video_preview' => 'courses/machine-learning-preview.mp4',
        //         'description' => 'Khóa học chuyên sâu về Machine Learning.',
        //         'primary_content' => 'Machine Learning, Python, AI',
        //         'status' => 'pending',
        //         'is_show_home' => 0,
        //         'target_students' => json_encode(['Lập trình viên AI', 'Sinh viên Khoa học dữ liệu']),
        //         'learning_outcomes' => json_encode(['Hiểu về Machine Learning', 'Xây dựng mô hình dự đoán']),
        //         'prerequisites' => json_encode(['Có kiến thức về lập trình Python']),
        //         'who_is_this_for' => 'Dành cho người muốn học AI.',
        //         'is_free' => 0,
        //         'language' => 'Tiếng Anh',
        //         'level' => 'Nâng cao',
        //         'admin_commission_rate' => 35.00,
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //         'submited_at' => Carbon::now(),
        //         'censored_at' => null,
        //         'admin_comment' => null,
        //     ],

        //     [
        //         'id' => Str::uuid(),
        //         'user_id' => 5,
        //         'category_id' => 3,
        //         'price_regular' => 900000,
        //         'price_sale' => 700000,
        //         'title' => 'Machine Learning với Python2',
        //         'thumbnail' => 'courses/machine-learning.jpg',
        //         'video_preview' => 'courses/machine-learning-preview.mp4',
        //         'description' => 'Khóa học chuyên sâu về Machine Learning.',
        //         'primary_content' => 'Machine Learning, Python, AI',
        //         'status' => 'pending',
        //         'is_show_home' => 0,
        //         'target_students' => json_encode(['Lập trình viên AI', 'Sinh viên Khoa học dữ liệu']),
        //         'learning_outcomes' => json_encode(['Hiểu về Machine Learning', 'Xây dựng mô hình dự đoán']),
        //         'prerequisites' => json_encode(['Có kiến thức về lập trình Python']),
        //         'who_is_this_for' => 'Dành cho người muốn học AI.',
        //         'is_free' => 0,
        //         'language' => 'Tiếng Anh',
        //         'level' => 'Nâng cao',
        //         'admin_commission_rate' => 35.00,
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //         'submited_at' => Carbon::now(),
        //         'censored_at' => null,
        //         'admin_comment' => null,
        //     ],
        //     [
        //         'id' => Str::uuid(),
        //         'user_id' => 5,
        //         'category_id' => 1,
        //         'price_regular' => 900000,
        //         'price_sale' => 700000,
        //         'title' => 'Machine Learning với Python3',
        //         'thumbnail' => 'courses/machine-learning.jpg',
        //         'video_preview' => 'courses/machine-learning-preview.mp4',
        //         'description' => 'Khóa học chuyên sâu về Machine Learning.',
        //         'primary_content' => 'Machine Learning, Python, AI',
        //         'status' => 'pending',
        //         'is_show_home' => 0,
        //         'target_students' => json_encode(['Lập trình viên AI', 'Sinh viên Khoa học dữ liệu']),
        //         'learning_outcomes' => json_encode(['Hiểu về Machine Learning', 'Xây dựng mô hình dự đoán']),
        //         'prerequisites' => json_encode(['Có kiến thức về lập trình Python']),
        //         'who_is_this_for' => 'Dành cho người muốn học AI.',
        //         'is_free' => 0,
        //         'language' => 'Tiếng Anh',
        //         'level' => 'Nâng cao',
        //         'admin_commission_rate' => 35.00,
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //         'submited_at' => Carbon::now(),
        //         'censored_at' => null,
        //         'admin_comment' => null,
        //     ],
        //     [
        //         'id' => Str::uuid(),
        //         'user_id' => 5,
        //         'category_id' => 2,
        //         'price_regular' => 900000,
        //         'price_sale' => 700000,
        //         'title' => 'Machine Learning với Python4',
        //         'thumbnail' => 'courses/machine-learning.jpg',
        //         'video_preview' => 'courses/machine-learning-preview.mp4',
        //         'description' => 'Khóa học chuyên sâu về Machine Learning.',
        //         'primary_content' => 'Machine Learning, Python, AI',
        //         'status' => 'pending',
        //         'is_show_home' => 0,
        //         'target_students' => json_encode(['Lập trình viên AI', 'Sinh viên Khoa học dữ liệu']),
        //         'learning_outcomes' => json_encode(['Hiểu về Machine Learning', 'Xây dựng mô hình dự đoán']),
        //         'prerequisites' => json_encode(['Có kiến thức về lập trình Python']),
        //         'who_is_this_for' => 'Dành cho người muốn học AI.',
        //         'is_free' => 0,
        //         'language' => 'Tiếng Anh',
        //         'level' => 'Nâng cao',
        //         'admin_commission_rate' => 35.00,
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //         'submited_at' => Carbon::now(),
        //         'censored_at' => null,
        //         'admin_comment' => null,
        //     ],
        //     [
        //         'id' => Str::uuid(),
        //         'user_id' => 3,
        //         'category_id' => 3,
        //         'price_regular' => 900000,
        //         'price_sale' => 700000,
        //         'title' => 'Machine Learning với Python5',
        //         'thumbnail' => 'courses/machine-learning.jpg',
        //         'video_preview' => 'courses/machine-learning-preview.mp4',
        //         'description' => 'Khóa học chuyên sâu về Machine Learning.',
        //         'primary_content' => 'Machine Learning, Python, AI',
        //         'status' => 'published',
        //         'is_show_home' => 0,
        //         'target_students' => json_encode(['Lập trình viên AI', 'Sinh viên Khoa học dữ liệu']),
        //         'learning_outcomes' => json_encode(['Hiểu về Machine Learning', 'Xây dựng mô hình dự đoán']),
        //         'prerequisites' => json_encode(['Có kiến thức về lập trình Python']),
        //         'who_is_this_for' => 'Dành cho người muốn học AI.',
        //         'is_free' => 0,
        //         'language' => 'Tiếng Anh',
        //         'level' => 'Nâng cao',
        //         'admin_commission_rate' => 35.00,
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //         'submited_at' => Carbon::now(),
        //         'censored_at' => null,
        //         'admin_comment' => null,
        //     ],
        //     [
        //         'id' => Str::uuid(),
        //         'user_id' => 2,
        //         'category_id' => 4,
        //         'price_regular' => 900000,
        //         'price_sale' => 700000,
        //         'title' => 'Machine Learning với Python',
        //         'thumbnail' => 'courses/machine-learning.jpg',
        //         'video_preview' => 'courses/machine-learning-preview.mp4',
        //         'description' => 'Khóa học chuyên sâu về Machine Learning.',
        //         'primary_content' => 'Machine Learning, Python, AI',
        //         'status' => 'draft',
        //         'is_show_home' => 0,
        //         'target_students' => json_encode(['Lập trình viên AI', 'Sinh viên Khoa học dữ liệu']),
        //         'learning_outcomes' => json_encode(['Hiểu về Machine Learning', 'Xây dựng mô hình dự đoán']),
        //         'prerequisites' => json_encode(['Có kiến thức về lập trình Python']),
        //         'who_is_this_for' => 'Dành cho người muốn học AI.',
        //         'is_free' => 0,
        //         'language' => 'Tiếng Anh',
        //         'level' => 'Nâng cao',
        //         'admin_commission_rate' => 35.00,
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //         'submited_at' => Carbon::now(),
        //         'censored_at' => null,
        //         'admin_comment' => null,
        //     ]
        // ];


        // foreach ($courses as $course) {
        //     Course::create($course);
        // }

    }
}
