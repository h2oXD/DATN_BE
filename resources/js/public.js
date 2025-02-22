import './bootstrap';
window.Echo.channel('admin-notifications')
        .listen('CourseApprovalRequested', (event) => {
            // Hiển thị thông báo cho Admin
            alert(`Khóa học ${event.courseId} cần phê duyệt!`);
            // Hoặc hiển thị thông báo đẹp hơn, cập nhật danh sách chờ phê duyệt
        });