 <li class="dropdown stopevent">
     <a class="btn btn-light btn-icon rounded-circle indicator indicator-primary" href="#" role="button"
         id="dropdownNotification" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
         <i class="fe fe-bell"></i>
     </a>
     <div class="dropdown-menu dropdown-menu-end dropdown-menu-lg" aria-labelledby="dropdownNotification"
         style="width: 500px">
         <div>
             <div class="border-bottom px-3 pb-3 d-flex justify-content-between align-items-center">
                 <span class="h4 mb-0">Thông báo</span>
                 <a href="# ">
                     <span class="align-middle">
                         <i class="fe fe-settings me-1"></i>
                     </span>
                 </a>
             </div>
             <!-- List group -->
             <ul class="list-group list-group-flush" data-simplebar style="max-height: 300px">
                 <ul class="p-0" id="idduynhat">
                     @if ($notifications->count() > 0)
                         @foreach ($notifications as $notification)
                             <li class="list-group-item bg-light" data-notification-id="{{ $notification->id }}">
                                 <div class="row">
                                     <div class="col">
                                         <a class="text-body" href="#">
                                             <div class="d-flex">
                                                 @if ($notification->user_avatar)
                                                     <img src="{{ Storage::url($notification->user_avatar) }}"
                                                         alt="" class="avatar-md rounded-circle" />
                                                 @else
                                                     <img src="https://i1.sndcdn.com/artworks-000641828677-d2ubdw-t500x500.jpg"
                                                         alt="" class="avatar-md rounded-circle" />
                                                 @endif
                                                 <a href="/admin/courses/{{ $notification->data['course_id'] }}/censor"
                                                     class="ms-3">
                                                     <h5 class="fw-bold mb-1">{{ $notification->data['user_name'] }}
                                                     </h5>
                                                     <p class="mb-3 message-content">
                                                         {{ $notification->data['message'] }}</p>
                                                     <span class="fs-6">
                                                         <span
                                                             class="ms-1">{{ $notification->created_at->format('H:i d/m/Y') }}</span>
                                                     </span>
                                                 </a>
                                             </div>
                                         </a>
                                     </div>
                                     <div class="col-auto text-center me-2">
                                         @if ($notification->read_at == null)
                                             <a href="#" class="badge-dot bg-info" data-bs-toggle="tooltip"
                                                 data-bs-placement="top" title="Chưa đọc"></a>
                                         @else
                                             <a href="#" class="badge-dot bg-secondary" data-bs-toggle="tooltip"
                                                 data-bs-placement="top" title="Đã đọc"></a>
                                         @endif
                                     </div>
                                 </div>
                             </li>
                         @endforeach
                     @endif
                 </ul>
             </ul>
             <div class="border-top px-3 pt-3 pb-0">
                 <a href="" class="text-link fw-semibold">Xem tất cả thông báo</a>
             </div>
         </div>
     </div>
 </li>
 <script>
     document.addEventListener('DOMContentLoaded', function() {
         const messageContents = document.querySelectorAll('.list-group-item');

         messageContents.forEach(messageContent => {
             messageContent.addEventListener('click', function() {
                 const notificationId = this.closest('li').dataset.notificationId;

                 fetch('api/notifications/' + notificationId + '/mark-as-read', {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'X-CSRF-TOKEN': '{{ csrf_token() }}' // Thêm CSRF token
                         }
                     })
                     .then(response => {
                         if (response.ok) {
                             // Cập nhật giao diện người dùng nếu cần
                             const badgeDot = this.closest('li').querySelector('.badge-dot');
                             if (badgeDot) {
                                 badgeDot.classList.remove('bg-info');
                                 badgeDot.classList.add('bg-secondary');
                                 badgeDot.setAttribute('title', 'Đã đọc');
                             }
                         } else {
                             console.error('Failed to mark notification as read.');
                         }
                     })
                     .catch(error => {
                         console.error('Error:', error);
                     });
             });
         });
     });
 </script>
