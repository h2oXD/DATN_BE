<!-- Tab Pending -->
<div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
    <div class="p-4 row">
        <div class="col-12 shadow-none rounded">
            <h4>Mục tiêu</h4>
            <ul>
                @if (isset($learning_outcomes))
                    @foreach ($learning_outcomes as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                @endif
            </ul>
        </div>
        <div class="col-12 shadow-none pt-3 rounded">
            <h4>Điều kiện tiên quyết để tham gia khóa học</h4>
            <ul>
                @if (isset($prerequisites))
                    @foreach ($prerequisites as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                @endif

            </ul>
        </div>
        <div class="col-12 shadow-none pt-3 rounded">
            <h4>Khóa học này dành cho đối tượng</h4>
            <ul>
                @if (isset($target_students))
                    @foreach ($target_students as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                @endif

            </ul>
        </div>
    </div>
</div>
