import "./bootstrap";
import { parseISO, isValid, format } from "date-fns";
console.log("Phía dưới real time" + userId);
// window.Echo.channel('admin-notifications')
//         .listen('CourseApprovalRequested', (event) => {
//             // Hiển thị thông báo cho Admin
//             alert(`Khóa học ${event.courseId} cần phê duyệt!`);
//             // Hoặc hiển thị thông báo đẹp hơn, cập nhật danh sách chờ phê duyệt
//         });
window.Echo.private("notifications." + userId).notification((notification) => {
    console.log(notification.data);
    console.log(notification);
    // Tạo HTML cho thông báo mới
    let newNotificationHtml = `
        <li class="list-group-item bg-light">
            <div class="row">
                <div class="col">
                    <a class="text-body" href="#">
                        <div class="d-flex">
                            ${
                                notification.user_avatar
                                    ? `<img src="${notification.user_avatar}" alt="" class="avatar-md rounded-circle" />`
                                    : `<img src="/assets/avatarDefault.jpg" alt="" class="avatar-md rounded-circle" />`
                            }
                            <div class="ms-3">
                            <a href="/admin/courses/${
                                notification.data.course_id
                            }/censor" class="ms-3">
                                <h5 class="fw-bold mb-1">${
                                    notification.data.user_name
                                }</h5>
                                <p class="mb-3">${notification.data.message}</p>
                                <span class="fs-6">
                                    <span class="ms-1">${new Date(
                                        notification.data["created_at"]
                                    ).toLocaleString()}</span>
                                </span>
                                </a>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-auto text-center me-2">
                    <a href="#" class="badge-dot bg-info" data-bs-toggle="tooltip" data-bs-placement="top" title="Chưa đọc"></a>
                </div>
            </div>
        </li>
    `;
    // Thêm HTML vào danh sách thông báo
    let notificationList = document.getElementById("idduynhat");
    if (notificationList) {
        notificationList.insertAdjacentHTML("afterbegin", newNotificationHtml);
    } else {
        console.error("Không tìm thấy phần tử có ID 'idduynhat'");
    }
});
